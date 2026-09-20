import 'dart:async';

import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

/// Cosmic AI — free AI astrologer chat. The app talks to the existing backend
/// proxy `POST /ask-master` (`ApiChatGPTController.askMaster`), which keeps
/// the provider API key server-side.
class CosmicAiScreen extends StatefulWidget {
  const CosmicAiScreen({super.key});

  @override
  State<CosmicAiScreen> createState() => _CosmicAiScreenState();
}

class _AiTurn {
  _AiTurn(this.text, {required this.mine});
  final String text;
  final bool mine;
}

class _CosmicAiScreenState extends State<CosmicAiScreen> {
  final _composer = TextEditingController();
  final _scroll = ScrollController();
  final List<_AiTurn> _turns = [];
  bool _thinking = false;
  Object? _error;

  static const _suggestions = [
    'What does today hold for me?',
    'Is this a good month to start a business?',
    'Explain my moon sign in simple words',
    'Which gemstone suits me?',
  ];

  @override
  void dispose() {
    _composer.dispose();
    _scroll.dispose();
    super.dispose();
  }

  Future<void> _ask(String text) async {
    final q = text.trim();
    if (q.isEmpty || _thinking) return;
    _composer.clear();
    setState(() {
      _turns.add(_AiTurn(q, mine: true));
      _thinking = true;
      _error = null;
    });
    _jump();
    try {
      final decoded = await ApiClient.instance
          .post('/ask-master', body: {'message': q});
      final answer = decoded is Map<String, dynamic>
          ? (decoded['message'] ?? decoded['answer'] ?? '').toString()
          : decoded.toString();
      if (!mounted) return;
      setState(() {
        _thinking = false;
        _turns.add(_AiTurn(answer.isEmpty ? '…' : answer, mine: false));
      });
      _jump();
    } catch (e) {
      if (mounted) {
        setState(() {
          _thinking = false;
          _error = e;
        });
      }
    }
  }

  void _jump() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scroll.hasClients) {
        _scroll.jumpTo(_scroll.position.maxScrollExtent);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(title: const Text('Cosmic AI · ✨')),
      body: Column(
        children: [
          Expanded(
            child: _turns.isEmpty
                ? ListView(
                    padding: const EdgeInsets.all(20),
                    children: [
                      const SizedBox(height: 24),
                      Icon(Icons.auto_awesome, size: 56, color: scheme.primary),
                      const SizedBox(height: 12),
                      Text('Ask the cosmos anything',
                          textAlign: TextAlign.center,
                          style: Theme.of(context)
                              .textTheme
                              .titleLarge
                              ?.copyWith(fontWeight: FontWeight.w800)),
                      const SizedBox(height: 6),
                      Text(
                        'Free AI astrologer. For personal guidance, chat with a '
                        'human astrologer in the Consult tab.',
                        textAlign: TextAlign.center,
                        style: Theme.of(context)
                            .textTheme
                            .bodyMedium
                            ?.copyWith(color: scheme.outline),
                      ),
                      const SizedBox(height: 24),
                      Wrap(
                        spacing: 8,
                        runSpacing: 8,
                        alignment: WrapAlignment.center,
                        children: [
                          for (final s in _suggestions)
                            ActionChip(
                              label: Text(s),
                              onPressed: () => _ask(s),
                            ),
                        ],
                      ),
                    ],
                  )
                : _error != null
                    ? StatusViews.error(context, _error!, onRetry: () {
                        setState(() => _error = null);
                      })
                    : ListView.builder(
                        controller: _scroll,
                        padding: const EdgeInsets.all(14),
                        itemCount: _turns.length + (_thinking ? 1 : 0),
                        itemBuilder: (context, i) {
                          if (i == _turns.length) {
                            return const Align(
                              alignment: Alignment.centerLeft,
                              child: Padding(
                                padding: EdgeInsets.all(12),
                                child: SizedBox(
                                  width: 22,
                                  height: 22,
                                  child: CircularProgressIndicator(
                                      strokeWidth: 2),
                                ),
                              ),
                            );
                          }
                          return _bubble(context, _turns[i]);
                        },
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
                      onSubmitted: _ask,
                      decoration: const InputDecoration(
                          hintText: 'Ask about love, career, health…'),
                    ),
                  ),
                  const SizedBox(width: 8),
                  CircleAvatar(
                    radius: 24,
                    backgroundColor: scheme.primary,
                    child: IconButton(
                      icon: const Icon(Icons.send_rounded,
                          color: Colors.white, size: 20),
                      onPressed: () => _ask(_composer.text),
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

  Widget _bubble(BuildContext context, _AiTurn t) {
    final scheme = Theme.of(context).colorScheme;
    return Align(
      alignment: t.mine ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.symmetric(vertical: 4),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        constraints:
            BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.78),
        decoration: BoxDecoration(
          color: t.mine ? scheme.primary : scheme.surface,
          borderRadius: BorderRadius.only(
            topLeft: const Radius.circular(16),
            topRight: const Radius.circular(16),
            bottomLeft: Radius.circular(t.mine ? 16 : 4),
            bottomRight: Radius.circular(t.mine ? 4 : 16),
          ),
          border: t.mine
              ? null
              : Border.all(color: scheme.outline.withValues(alpha: 0.5)),
        ),
        child: Text(
          t.text,
          style: TextStyle(
              color: t.mine ? Colors.white : scheme.onSurface, height: 1.35),
        ),
      ),
    );
  }
}
