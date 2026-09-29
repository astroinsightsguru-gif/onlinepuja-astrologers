import 'dart:async';

import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';

/// Partner-side 1:1 chat session (legacy chat session screen). Polls REST
/// history until Laravel Reverb websockets are enabled server-side.
class ChatSessionScreen extends StatefulWidget {
  const ChatSessionScreen({
    super.key,
    required this.customerId,
    required this.customerName,
    this.sessionId,
  });

  static const route = '/chat';

  final int customerId;
  final String customerName;
  final String? sessionId;

  @override
  State<ChatSessionScreen> createState() => _ChatSessionScreenState();
}

class _ChatSessionScreenState extends State<ChatSessionScreen> {
  final _composer = TextEditingController();
  final _scroll = ScrollController();
  Timer? _poller;
  String? _sessionId;
  List<ChatMessage> _messages = const [];
  bool _sending = false;
  Object? _error;

  String get _myId => context.read<PartnerSession>().myId;

  @override
  void initState() {
    super.initState();
    _sessionId = widget.sessionId;
    _start();
  }

  @override
  void dispose() {
    _poller?.cancel();
    _composer.dispose();
    _scroll.dispose();
    super.dispose();
  }

  Future<void> _start() async {
    try {
      final session = context.read<PartnerSession>();
      if (!session.isLoggedIn || session.astrologerId == 0) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Partner session expired. Please log in.')),
          );
          Navigator.of(context).pop();
        }
        return;
      }
      if (_sessionId == null && widget.customerId > 0) {
        _sessionId ??= await AstrologerApi.instance.addChatRequest(
          astrologerId: session.astrologerId,
          userId: widget.customerId,
        );
      }
      if (!mounted) return;
      await _fetch();
      _poller = Timer.periodic(const Duration(seconds: 3), (_) => _fetch());
      setState(() {});
    } catch (e) {
      if (mounted) setState(() => _error = e);
    }
  }

  Future<void> _fetch({bool forceScroll = false}) async {
    if (_sessionId == null) return;
    final fresh = await AstrologerApi.instance
        .chatHistory(sessionId: _sessionId!, myId: _myId);
    if (!mounted) return;
    final hasNewMessages = fresh.length > _messages.length;
    setState(() => _messages = fresh);
    if (forceScroll || hasNewMessages) {
      _jumpToBottom();
    }
  }

  void _jumpToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scroll.hasClients) {
        _scroll.jumpTo(_scroll.position.maxScrollExtent);
      }
    });
  }

  Future<void> _send() async {
    final text = _composer.text.trim();
    if (text.isEmpty || _sessionId == null || _sending) return;
    setState(() => _sending = true);
    _composer.clear();
    try {
      await AstrologerApi.instance.sendMessage(
        sessionId: _sessionId!,
        fromUserId: _myId,
        text: text,
      );
      await _fetch(forceScroll: true);
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  Future<void> _endChat() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('End chat?'),
        content: const Text('This closes the session and stops billing.'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(false),
            child: const Text('Cancel'),
          ),
          FilledButton(
            onPressed: () => Navigator.of(ctx).pop(true),
            child: const Text('End'),
          ),
        ],
      ),
    );
    if (confirmed != true || !mounted) return;
    _poller?.cancel();
    if (_sessionId != null) {
      try {
        await AstrologerApi.instance.endChat(sessionId: _sessionId!);
      } catch (_) {/* best-effort */}
    }
    if (mounted) Navigator.of(context).pop();
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(widget.customerName),
            Text(
              'Chat session',
              style: Theme.of(context)
                  .textTheme
                  .labelSmall
                  ?.copyWith(color: scheme.outline),
            ),
          ],
        ),
        actions: [
          IconButton(
            tooltip: 'Client Info & Kundli',
            icon: const Icon(Icons.info_outline_rounded),
            onPressed: _showClientInfoSheet,
          ),
          IconButton(
            tooltip: 'End chat',
            icon: const Icon(Icons.call_end_rounded),
            onPressed: _endChat,
          ),
        ],
      ),
      body: _error != null
          ? StatusViews.error(context, _error!, onRetry: _start)
          : Column(
              children: [
                Expanded(
                  child: _messages.isEmpty
                      ? StatusViews.empty(context,
                          message: 'Waiting for the customer…')
                      : ListView.builder(
                          controller: _scroll,
                          padding: const EdgeInsets.all(14),
                          itemCount: _messages.length,
                          itemBuilder: (context, i) =>
                              _bubble(context, _messages[i]),
                        ),
                ),
                SafeArea(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(12, 6, 12, 10),
                    child: Row(
                      children: [
                        Expanded(
                          child: TextField(
                            controller: _composer,
                            minLines: 1,
                            maxLines: 4,
                            textInputAction: TextInputAction.send,
                            onSubmitted: (_) => _send(),
                            decoration: const InputDecoration(
                                hintText: 'Type your reply…'),
                          ),
                        ),
                        const SizedBox(width: 8),
                        CircleAvatar(
                          radius: 24,
                          backgroundColor: scheme.primary,
                          child: IconButton(
                            icon: const Icon(Icons.send_rounded,
                                color: Colors.white, size: 20),
                            onPressed: _send,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
    );
  }

  Widget _bubble(BuildContext context, ChatMessage m) {
    final scheme = Theme.of(context).colorScheme;
    final mine = m.isMine;
    return Align(
      alignment: mine ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.symmetric(vertical: 4),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        constraints: BoxConstraints(
            maxWidth: MediaQuery.of(context).size.width * 0.75),
        decoration: BoxDecoration(
          color: mine ? scheme.primary : scheme.surface,
          borderRadius: BorderRadius.only(
            topLeft: const Radius.circular(16),
            topRight: const Radius.circular(16),
            bottomLeft: Radius.circular(mine ? 16 : 4),
            bottomRight: Radius.circular(mine ? 4 : 16),
          ),
          border:
              mine ? null : Border.all(color: scheme.outline.withValues(alpha: 0.5)),
        ),
        child: Text(
          m.text,
          style: TextStyle(
              color: mine ? Colors.white : scheme.onSurface, height: 1.3),
        ),
      ),
    );
  }

  void _showClientInfoSheet() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.all(20),
        child: FutureBuilder<Map<String, dynamic>?>(
          future: AstrologerApi.instance.getIntakeForm(userId: widget.customerId),
          builder: (context, snapshot) {
            final intake = snapshot.data;
            final dob = intake?['birthDate']?.toString() ?? 'Not specified';
            final tob = intake?['birthTime']?.toString() ?? 'Not specified';
            final pob = intake?['birthPlace']?.toString() ?? 'Not specified';
            final topic = intake?['topicOfConcern']?.toString() ?? 'General Consultation';
            final occupation = intake?['occupation']?.toString();

            return Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    const Icon(Icons.account_circle, color: AppTheme.brandSaffron, size: 28),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Text(
                        widget.customerName,
                        style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                      ),
                    ),
                    IconButton(
                      icon: const Icon(Icons.close),
                      onPressed: () => Navigator.pop(ctx),
                    ),
                  ],
                ),
                const Divider(),
                const SizedBox(height: 8),
                Text('Client ID: #${widget.customerId}', style: TextStyle(color: Theme.of(context).colorScheme.outline)),
                const SizedBox(height: 12),
                const Text('Kundli & Birth Details:', style: TextStyle(fontWeight: FontWeight.w600)),
                const SizedBox(height: 6),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Theme.of(context).colorScheme.surfaceContainerHighest.withValues(alpha: 0.5),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Column(
                    children: [
                      _infoRow(context, 'Date of Birth', dob),
                      const SizedBox(height: 4),
                      _infoRow(context, 'Time of Birth', tob),
                      const SizedBox(height: 4),
                      _infoRow(context, 'Place of Birth', pob),
                      const SizedBox(height: 4),
                      _infoRow(context, 'Concern', topic),
                      if (occupation != null && occupation.isNotEmpty) ...[
                        const SizedBox(height: 4),
                        _infoRow(context, 'Occupation', occupation),
                      ],
                    ],
                  ),
                ),
                const SizedBox(height: 14),
                AiCopilotCard(
                  clientName: widget.customerName,
                  dob: dob,
                  tob: tob,
                  pob: pob,
                  concern: topic,
                ),
                const SizedBox(height: 16),
              ],
            );
          },
        ),
      ),
    );
  }

  Widget _infoRow(BuildContext context, String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: TextStyle(color: Theme.of(context).colorScheme.outline, fontSize: 13)),
        Text(value, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
      ],
    );
  }
}

