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
      if (_sessionId == null && widget.customerId > 0) {
        _sessionId ??= await AstrologerApi.instance.addChatRequest(
          astrologerId: context.read<PartnerSession>().astrologerId,
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

  Future<void> _fetch() async {
    if (_sessionId == null) return;
    final fresh = await AstrologerApi.instance
        .chatHistory(sessionId: _sessionId!, myId: _myId);
    if (!mounted) return;
    setState(() => _messages = fresh);
    _jumpToBottom();
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
      await _fetch();
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
}

