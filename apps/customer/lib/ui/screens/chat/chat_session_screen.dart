import 'dart:async';

import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';

/// 1:1 consultation chat. v2 polls the REST history every 3s until Laravel
/// Reverb websockets are enabled server-side (docs/new-apps/05 §5.3).
class ChatSessionScreen extends StatefulWidget {
  const ChatSessionScreen({
    super.key,
    required this.astrologerId,
    required this.astrologerName,
    this.sessionId,
    this.isFree = false,
  });

  static const route = '/chat';

  final int astrologerId;
  final String astrologerName;
  final String? sessionId;
  final bool isFree;

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
  Duration _elapsed = Duration.zero;
  DateTime? _sessionStart;

  String get _myId => context.read<AppSession>().myId;

  @override
  void initState() {
    super.initState();
    _sessionId = widget.sessionId;
    _openOrReuse();
  }

  @override
  void dispose() {
    _poller?.cancel();
    _composer.dispose();
    _scroll.dispose();
    super.dispose();
  }

  Future<void> _openOrReuse() async {
    final userId = context.read<AppSession>().user?.id ?? 0;
    try {
      _sessionId ??= await AstrologerApi.instance
          .existingChatSession(widget.astrologerId);
      _sessionId ??= await AstrologerApi.instance.addChatRequest(
        astrologerId: widget.astrologerId,
        userId: userId,
        isFree: widget.isFree,
      );
      _sessionStart ??= DateTime.now();
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
    setState(() {
      _messages = fresh;
      if (_sessionStart != null) {
        _elapsed = DateTime.now().difference(_sessionStart!);
      }
    });
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
        content: const Text(
            'Your consultation minutes will be settled and the session closed.'),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(ctx, false),
              child: const Text('Cancel')),
          FilledButton(
              onPressed: () => Navigator.pop(ctx, true),
              child: const Text('End chat')),
        ],
      ),
    );
    if (confirmed != true || _sessionId == null) return;
    try {
      await AstrologerApi.instance.endChat(sessionId: _sessionId!);
    } catch (_) {}
    if (mounted) Navigator.of(context).pop();
  }

  String get _timerLabel {
    final m = _elapsed.inMinutes.remainder(60).toString().padLeft(2, '0');
    final s = _elapsed.inSeconds.remainder(60).toString().padLeft(2, '0');
    return '${_elapsed.inHours}:$m:$s';
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(widget.astrologerName,
                style: Theme.of(context).textTheme.titleMedium),
            Text(_timerLabel,
                style: Theme.of(context)
                    .textTheme
                    .labelSmall
                    ?.copyWith(color: scheme.outline)),
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
          ? StatusViews.error(context, _error!, onRetry: _openOrReuse)
          : Column(
              children: [
                Expanded(
                  child: _messages.isEmpty
                      ? StatusViews.empty(context,
                          message: 'Say 🙏 namaste to begin…')
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
                            decoration:
                                const InputDecoration(hintText: 'Type your question…'),
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
