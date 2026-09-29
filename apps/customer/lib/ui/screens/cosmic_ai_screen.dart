import 'dart:async';

import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

/// Cosmic AI — free AI astrologer chat. The app talks to the existing backend
/// proxy `POST /ask-master` (`ApiChatGPTController.askMaster`), which keeps
/// the provider API key server-side.
class CosmicAiScreen extends StatefulWidget {
  const CosmicAiScreen({super.key});

  static const route = '/cosmic-ai';

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
      final dynamic rl = (decoded is Map<String, dynamic>) ? decoded['recordList'] : null;
      final answer = (rl is Map<String, dynamic> ? rl['reply'] : null) ??
          (decoded is Map<String, dynamic> ? (decoded['reply'] ?? decoded['message'] ?? decoded['answer']) : null) ??
          decoded?.toString() ??
          '';
      if (!mounted) return;
      setState(() {
        _thinking = false;
        _turns.add(_AiTurn(answer.toString().trim().isEmpty ? 'Pranam. The cosmic energies are aligning. Please ask again.' : answer.toString().trim(), mine: false));
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
                      Text('Popular Inquiries',
                          style: Theme.of(context).textTheme.titleSmall?.copyWith(
                                fontWeight: FontWeight.bold,
                                color: scheme.primary,
                              )),
                      const SizedBox(height: 10),
                      Wrap(
                        spacing: 8,
                        runSpacing: 8,
                        alignment: WrapAlignment.center,
                        children: [
                          for (final s in _suggestions)
                            ActionChip(
                              avatar: const Icon(Icons.auto_awesome, size: 14),
                              label: Text(s),
                              onPressed: () => _ask(s),
                            ),
                        ],
                      ),
                    ],
                  )
                : _error != null
                    ? (_error.toString().contains('403') || _error.toString().contains('logged in'))
                        ? Center(
                            child: Padding(
                              padding: const EdgeInsets.all(24),
                              child: Column(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Icon(Icons.lock_person_rounded, size: 54, color: scheme.primary),
                                  const SizedBox(height: 14),
                                  Text(
                                    'Login Required',
                                    style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.bold),
                                  ),
                                  const SizedBox(height: 6),
                                  Text(
                                    'Please log in to consult the cosmic AI astrologer with personalized charts.',
                                    textAlign: TextAlign.center,
                                    style: TextStyle(color: scheme.outline, fontSize: 13),
                                  ),
                                  const SizedBox(height: 18),
                                  FilledButton.icon(
                                    onPressed: () => Navigator.of(context).pushNamed('/login'),
                                    icon: const Icon(Icons.login_rounded, size: 18),
                                    label: const Text('Log In with Phone'),
                                  ),
                                ],
                              ),
                            ),
                          )
                        : StatusViews.error(context, _error!, onRetry: () {
                            setState(() => _error = null);
                          })
                    : ListView.builder(
                        controller: _scroll,
                        padding: const EdgeInsets.all(14),
                        itemCount: _turns.length + (_thinking ? 1 : 0) + (_turns.length >= 2 ? 1 : 0),
                        itemBuilder: (context, i) {
                          if (i == _turns.length && _thinking) {
                            return const Align(
                              alignment: Alignment.centerLeft,
                              child: Padding(
                                padding: EdgeInsets.all(12),
                                child: SizedBox(
                                  width: 22,
                                  height: 22,
                                  child: CircularProgressIndicator(strokeWidth: 2),
                                ),
                              ),
                            );
                          }
                          if (i >= _turns.length) {
                            // Smart Astrologer Consultation Card
                            return Container(
                              margin: const EdgeInsets.only(top: 14, bottom: 8),
                              padding: const EdgeInsets.all(14),
                              decoration: BoxDecoration(
                                color: scheme.primaryContainer.withValues(alpha: 0.35),
                                borderRadius: BorderRadius.circular(14),
                                border: Border.all(color: scheme.primary.withValues(alpha: 0.3)),
                              ),
                              child: Row(
                                children: [
                                  CircleAvatar(
                                    backgroundColor: scheme.primary,
                                    child: const Icon(Icons.support_agent, color: Colors.white, size: 20),
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text('Want deeper Vedic insights?',
                                            style: TextStyle(
                                                fontWeight: FontWeight.bold,
                                                color: scheme.onSurface)),
                                        const SizedBox(height: 2),
                                        Text('Consult live verified astrologers for personalized remedies.',
                                            style: TextStyle(fontSize: 12, color: scheme.outline)),
                                      ],
                                    ),
                                  ),
                                  const SizedBox(width: 8),
                                  FilledButton.tonal(
                                    onPressed: () {
                                      Navigator.of(context).pop();
                                    },
                                    child: const Text('Consult', style: TextStyle(fontSize: 12)),
                                  ),
                                ],
                              ),
                            );
                          }
                          return _bubble(context, _turns[i]);
                        },
                      ),
          ),
          if (_turns.isNotEmpty && !_thinking)
            Container(
              height: 38,
              padding: const EdgeInsets.symmetric(horizontal: 10),
              child: ListView(
                scrollDirection: Axis.horizontal,
                children: [
                  for (final prompt in [
                    '❤️ Love & Marriage',
                    '💼 Career & Wealth',
                    '🩺 Health & Peace',
                    '🔮 Lucky Gemstone',
                    '✨ Remedies for me',
                  ])
                    Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: ActionChip(
                        avatar: const Icon(Icons.auto_awesome, size: 13),
                        label: Text(prompt, style: const TextStyle(fontSize: 12)),
                        padding: const EdgeInsets.symmetric(horizontal: 4),
                        materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                        onPressed: () => _ask(prompt),
                      ),
                    ),
                ],
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
        margin: const EdgeInsets.symmetric(vertical: 5),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        constraints:
            BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.88),
        decoration: BoxDecoration(
          color: t.mine
              ? AppTheme.brandSaffron
              : scheme.surface,
          borderRadius: BorderRadius.only(
            topLeft: const Radius.circular(18),
            topRight: const Radius.circular(18),
            bottomLeft: Radius.circular(t.mine ? 18 : 4),
            bottomRight: Radius.circular(t.mine ? 4 : 18),
          ),
          border: t.mine
              ? null
              : Border.all(color: scheme.outline.withValues(alpha: 0.3)),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.04),
              blurRadius: 6,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: _buildFormattedMessage(context, t.text, scheme, t.mine),
      ),
    );
  }

  Widget _buildFormattedMessage(BuildContext context, String text, ColorScheme scheme, bool isMine) {
    if (isMine) {
      return Text(
        text,
        style: const TextStyle(color: Colors.white, fontSize: 14, height: 1.35, fontWeight: FontWeight.w500),
      );
    }

    final lines = text.split('\n');
    final List<Widget> widgets = [];
    final numberedRegex = RegExp(r'^(\d+[\.\)])\s*(.*)');

    for (int i = 0; i < lines.length; i++) {
      final line = lines[i].trim();
      if (line.isEmpty) {
        widgets.add(const SizedBox(height: 6));
        continue;
      }

      if (line == '---' || line == '***') {
        widgets.add(Divider(
          height: 18,
          thickness: 1,
          color: scheme.outline.withValues(alpha: 0.2),
        ));
        continue;
      }

      if (line.startsWith('### ') || line.startsWith('## ') || line.startsWith('# ')) {
        final headingText = line.replaceFirst(RegExp(r'^#+\s*'), '');
        widgets.add(Padding(
          padding: const EdgeInsets.only(top: 8, bottom: 4),
          child: Text(
            headingText,
            style: const TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.w800,
              color: AppTheme.brandDeep,
              height: 1.25,
            ),
          ),
        ));
        continue;
      }

      if (line.startsWith('- ') || line.startsWith('* ') || line.startsWith('• ')) {
        final bulletContent = line.replaceFirst(RegExp(r'^[-*•]\s*'), '');
        widgets.add(Padding(
          padding: const EdgeInsets.only(left: 4, bottom: 4),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                '• ',
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: scheme.primary,
                ),
              ),
              Expanded(
                child: _buildInlineMarkdown(bulletContent, scheme),
              ),
            ],
          ),
        ));
        continue;
      }

      final numberMatch = numberedRegex.firstMatch(line);
      if (numberMatch != null) {
        final numPrefix = numberMatch.group(1)!;
        final restContent = numberMatch.group(2)!;
        widgets.add(Padding(
          padding: const EdgeInsets.only(left: 4, bottom: 4),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                '$numPrefix ',
                style: TextStyle(
                  fontSize: 13.5,
                  fontWeight: FontWeight.bold,
                  color: scheme.primary,
                ),
              ),
              Expanded(
                child: _buildInlineMarkdown(restContent, scheme),
              ),
            ],
          ),
        ));
        continue;
      }

      widgets.add(Padding(
        padding: const EdgeInsets.only(bottom: 4),
        child: _buildInlineMarkdown(line, scheme),
      ));
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      mainAxisSize: MainAxisSize.min,
      children: widgets,
    );
  }

  Widget _buildInlineMarkdown(String text, ColorScheme scheme) {
    final spans = <TextSpan>[];
    final regex = RegExp(r'\*\*(.*?)\*\*');
    int lastIndex = 0;

    for (final match in regex.allMatches(text)) {
      if (match.start > lastIndex) {
        spans.add(TextSpan(
          text: text.substring(lastIndex, match.start),
          style: TextStyle(color: scheme.onSurface, fontSize: 13.5, height: 1.4),
        ));
      }
      spans.add(TextSpan(
        text: match.group(1),
        style: TextStyle(
          color: scheme.onSurface,
          fontWeight: FontWeight.bold,
          fontSize: 13.5,
          height: 1.4,
        ),
      ));
      lastIndex = match.end;
    }

    if (lastIndex < text.length) {
      spans.add(TextSpan(
        text: text.substring(lastIndex),
        style: TextStyle(color: scheme.onSurface, fontSize: 13.5, height: 1.4),
      ));
    }

    return RichText(
      text: TextSpan(children: spans),
    );
  }
}
