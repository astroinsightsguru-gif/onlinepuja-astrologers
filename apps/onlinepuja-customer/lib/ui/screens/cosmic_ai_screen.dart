import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';
import '../../state/app_session.dart';
import '../theme/customer_theme.dart';
import 'main_shell.dart';

/// Online Puja Master Vedic AI — High Quality Intelligent Astrology Chatbot
/// Integrated with Growth OS AI Engine & Master Brain failover chain.
class OnlinePujaAiScreen extends StatefulWidget {
  const OnlinePujaAiScreen({super.key});

  static const route = '/onlinepuja-ai';

  @override
  State<OnlinePujaAiScreen> createState() => _OnlinePujaAiScreenState();
}

/// Backward compatibility alias — merged into unified OnlinePuja AI
typedef CosmicAiScreen = OnlinePujaAiScreen;

class _AiTurn {
  _AiTurn(this.text, {required this.mine, this.timestamp, this.suggestions});
  final String text;
  final bool mine;
  final DateTime? timestamp;
  final List<String>? suggestions;
}

class _OnlinePujaAiScreenState extends State<OnlinePujaAiScreen> {
  final TextEditingController _composer = TextEditingController();
  final ScrollController _scroll = ScrollController();
  final List<_AiTurn> _turns = [];
  bool _thinking = false;
  Object? _error;
  String _selectedTopic = 'All';

  static const List<Map<String, String>> _modes = [
    {'id': 'All', 'label': 'Divine Guidance', 'icon': '🕉️'},
    {'id': 'Kundli', 'label': 'Kundli & Dasha', 'icon': '🪐'},
    {'id': 'Career', 'label': 'Career & Artha', 'icon': '💼'},
    {'id': 'Love', 'label': 'Love & Vivah', 'icon': '❤️'},
    {'id': 'Remedies', 'label': 'Vedic Upay & Puja', 'icon': '🪔'},
    {'id': 'Health', 'label': 'Swasthya & Peace', 'icon': '🩺'},
  ];

  static const List<String> _initialPrompts = [
    'What do my planetary transits (Gochar) indicate today?',
    'Which puja or mantra will remove obstacles in my career?',
    'Explain my Moon sign & Nakshatra characteristics',
    'Which sacred gemstone & Rudraksha is auspicious for me?',
    'Guidance on relationship harmony and Kundli Gun Milan',
  ];

  @override
  void initState() {
    super.initState();
    final aiCfg = GrowthAiOsService.instance.aiConfig;
    // Add warm welcome from Master Vedic AI dynamically configured via Growth & AI OS
    _turns.add(_AiTurn(
      aiCfg.welcomeGreeting,
      mine: false,
      timestamp: DateTime.now(),
      suggestions: aiCfg.starterPrompts.take(3).toList(),
    ));
  }

  @override
  void dispose() {
    _composer.dispose();
    _scroll.dispose();
    super.dispose();
  }

  Future<void> _ask(String text) async {
    final q = text.trim();
    if (q.isEmpty || _thinking) return;

    HapticFeedback.lightImpact();
    _composer.clear();
    setState(() {
      _turns.add(_AiTurn(q, mine: true, timestamp: DateTime.now()));
      _thinking = true;
      _error = null;
    });
    _jump();

    try {
      AppSession? session;
      try {
        session = context.read<AppSession>();
      } catch (_) {}

      final user = session?.user;
      final lang = LocaleManager.instance.currentLanguage.value.code;

      final body = {
        'message': q,
        'language': lang,
        'mode': _selectedTopic,
        if (user != null)
          'context': {
            'name': user.name,
            'birthDate': user.birthDate,
            'birthPlace': user.birthPlace,
          },
      };

      final decoded = await ApiClient.instance.post('/ask-master', body: body);

      final dynamic rl = (decoded is Map<String, dynamic>) ? decoded['recordList'] : null;
      final answer = (rl is Map<String, dynamic> ? rl['reply'] : null) ??
          (decoded is Map<String, dynamic> ? (decoded['message'] ?? decoded['reply'] ?? decoded['answer']) : null) ??
          decoded?.toString() ??
          '';

      final cleanAnswer = answer.toString().trim();
      final replyText = cleanAnswer.isEmpty
          ? '🕉️ Pranam. The planetary energies are aligning. Please ask your divine inquiry again.'
          : cleanAnswer;

      if (!mounted) return;
      setState(() {
        _thinking = false;
        _turns.add(_AiTurn(
          replyText,
          mine: false,
          timestamp: DateTime.now(),
          suggestions: _generateDynamicFollowUps(q),
        ));
      });
      _jump();
    } catch (e) {
      if (mounted) {
        setState(() {
          _thinking = false;
          _error = e;
        });
        _jump();
      }
    }
  }

  List<String> _generateDynamicFollowUps(String query) {
    final lq = query.toLowerCase();
    if (lq.contains('career') || lq.contains('job') || lq.contains('business') || lq.contains('money')) {
      return [
        'Which day is auspicious for career interviews?',
        'Suggest remedies for financial prosperity',
        'Recommend a Kubera or Lakshmi Puja',
      ];
    }
    if (lq.contains('love') || lq.contains('marriage') || lq.contains('relationship') || lq.contains('match')) {
      return [
        'How does Venus (Shukra) influence my chart?',
        'Remedies for Mangal / Manglik dosha',
        'Suggest puja for marital bliss',
      ];
    }
    return [
      'Which gemstone or Rudraksha suits me?',
      'Suggest a sacred havan for peace & health',
      'Tell me today\'s most auspicious Choghadiya',
    ];
  }

  void _jump() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scroll.hasClients) {
        _scroll.animateTo(
          _scroll.position.maxScrollExtent,
          duration: const Duration(milliseconds: 280),
          curve: Curves.easeOut,
        );
      }
    });
  }

  void _copyToClipboard(String text) {
    Clipboard.setData(ClipboardData(text: text));
    HapticFeedback.mediumImpact();
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Vedic advice copied to clipboard 🕉️'),
        duration: Duration(seconds: 2),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final scheme = Theme.of(context).colorScheme;

    return Scaffold(
      backgroundColor: isDark ? const Color(0xFF0F0B1E) : const Color(0xFFFBF8F2),
      appBar: AppBar(
        elevation: 0,
        backgroundColor: isDark ? const Color(0xFF1B132E) : Colors.white,
        titleSpacing: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 20),
          onPressed: () => Navigator.of(context).maybePop(),
        ),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(2),
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                gradient: CustomerTheme.goldGradient,
                boxShadow: [
                  BoxShadow(
                    color: CustomerTheme.brandGold.withValues(alpha: 0.4),
                    blurRadius: 8,
                  ),
                ],
              ),
              child: const CircleAvatar(
                radius: 18,
                backgroundColor: Color(0xFF451A03),
                child: Text('ॐ', style: TextStyle(color: Color(0xFFFDE68A), fontSize: 18, fontWeight: FontWeight.bold)),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  Row(
                    children: [
                      Flexible(
                        child: Text(
                          GrowthAiOsService.instance.brandConfig.brandName + ' AI',
                          style: TextStyle(
                            fontSize: 15.5,
                            fontWeight: FontWeight.w800,
                            color: isDark ? Colors.white : const Color(0xFF1E293B),
                          ),
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      const SizedBox(width: 6),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1.5),
                        decoration: BoxDecoration(
                          color: const Color(0xFFD97706),
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: const Text(
                          'GROWTH OS',
                          style: TextStyle(fontSize: 8.5, fontWeight: FontWeight.w900, color: Colors.white),
                        ),
                      ),
                    ],
                  ),
                  Row(
                    children: [
                      Container(
                        width: 6,
                        height: 6,
                        decoration: const BoxDecoration(
                          color: Color(0xFF10B981),
                          shape: BoxShape.circle,
                        ),
                      ),
                      const SizedBox(width: 5),
                      Text(
                        '${GrowthAiOsService.instance.aiConfig.botName} • ${GrowthAiOsService.instance.aiConfig.botTagline}',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w500,
                          color: isDark ? Colors.white70 : Colors.black54,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded),
            tooltip: 'Clear Chat',
            onPressed: () {
              setState(() {
                _turns.clear();
                _turns.add(_AiTurn(
                  '🕉️ **सादर प्रणाम।** New consultation started. How may I assist your astrological chart now?',
                  mine: false,
                  timestamp: DateTime.now(),
                ));
              });
            },
          ),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(44),
          child: Container(
            height: 44,
            decoration: BoxDecoration(
              color: isDark ? const Color(0xFF171026) : const Color(0xFFF9F5EC),
              border: Border(
                bottom: BorderSide(
                  color: isDark ? Colors.white10 : Colors.black.withValues(alpha: 0.06),
                ),
              ),
            ),
            child: ListView.separated(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              itemCount: _modes.length,
              separatorBuilder: (_, _) => const SizedBox(width: 8),
              itemBuilder: (context, i) {
                final m = _modes[i];
                final selected = _selectedTopic == m['id'];
                return InkWell(
                  onTap: () {
                    HapticFeedback.selectionClick();
                    setState(() => _selectedTopic = m['id']!);
                  },
                  borderRadius: BorderRadius.circular(20),
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                    decoration: BoxDecoration(
                      gradient: selected ? CustomerTheme.saffronGradient : null,
                      color: selected
                          ? null
                          : isDark
                              ? const Color(0xFF241A3E)
                              : Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(
                        color: selected
                            ? Colors.transparent
                            : isDark
                                ? Colors.white12
                                : Colors.black.withValues(alpha: 0.08),
                      ),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(m['icon']!, style: const TextStyle(fontSize: 12)),
                        const SizedBox(width: 5),
                        Text(
                          m['label']!,
                          style: TextStyle(
                            fontSize: 11.5,
                            fontWeight: selected ? FontWeight.w700 : FontWeight.w500,
                            color: selected
                                ? Colors.white
                                : isDark
                                    ? Colors.white70
                                    : Colors.black87,
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),
        ),
      ),
      body: Column(
        children: [
          Expanded(
            child: ListView.builder(
              controller: _scroll,
              padding: const EdgeInsets.fromLTRB(14, 14, 14, 14),
              itemCount: _turns.length + (_thinking ? 1 : 0),
              itemBuilder: (context, i) {
                if (i == _turns.length && _thinking) {
                  return _buildThinkingBubble(isDark);
                }
                return _buildTurnBubble(context, _turns[i], isDark);
              },
            ),
          ),

          // Quick prompt chips
          if (!_thinking)
            Container(
              height: 40,
              padding: const EdgeInsets.symmetric(horizontal: 8),
              child: ListView.separated(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 6),
                itemCount: _initialPrompts.length,
                separatorBuilder: (_, _) => const SizedBox(width: 8),
                itemBuilder: (context, i) {
                  final prompt = _initialPrompts[i];
                  return ActionChip(
                    avatar: const Icon(Icons.auto_awesome, size: 12, color: Color(0xFFD97706)),
                    label: Text(prompt, style: const TextStyle(fontSize: 11.5, fontWeight: FontWeight.w600)),
                    backgroundColor: isDark ? const Color(0xFF23193D) : Colors.white,
                    side: BorderSide(color: isDark ? Colors.white12 : const Color(0xFFE5E7EB)),
                    padding: const EdgeInsets.symmetric(horizontal: 4),
                    onPressed: () => _ask(prompt),
                  );
                },
              ),
            ),

          // Composer Input Bar
          SafeArea(
            top: false,
            child: Container(
              padding: const EdgeInsets.fromLTRB(12, 8, 12, 10),
              decoration: BoxDecoration(
                color: isDark ? const Color(0xFF1B132E) : Colors.white,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: isDark ? 0.4 : 0.06),
                    blurRadius: 10,
                    offset: const Offset(0, -3),
                  ),
                ],
              ),
              child: Row(
                children: [
                  Expanded(
                    child: Container(
                      decoration: BoxDecoration(
                        color: isDark ? const Color(0xFF281E45) : const Color(0xFFF3F4F6),
                        borderRadius: BorderRadius.circular(24),
                        border: Border.all(
                          color: isDark ? Colors.white12 : Colors.black.withValues(alpha: 0.08),
                        ),
                      ),
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      child: TextField(
                        controller: _composer,
                        minLines: 1,
                        maxLines: 4,
                        textInputAction: TextInputAction.send,
                        onSubmitted: _ask,
                        style: TextStyle(
                          color: isDark ? Colors.white : Colors.black87,
                          fontSize: 14,
                        ),
                        decoration: InputDecoration(
                          hintText: 'Ask your astrological or puja question…',
                          hintStyle: TextStyle(
                            color: isDark ? Colors.white38 : Colors.black38,
                            fontSize: 13.5,
                          ),
                          border: InputBorder.none,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Container(
                    decoration: const BoxDecoration(
                      shape: BoxShape.circle,
                      gradient: CustomerTheme.saffronGradient,
                    ),
                    child: IconButton(
                      icon: const Icon(Icons.send_rounded, color: Colors.white, size: 20),
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

  Widget _buildThinkingBubble(bool isDark) {
    return Align(
      alignment: Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.symmetric(vertical: 8),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          color: isDark ? const Color(0xFF23193D) : Colors.white,
          borderRadius: BorderRadius.circular(18),
          border: Border.all(color: const Color(0xFFD97706).withValues(alpha: 0.3)),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            const SizedBox(
              width: 16,
              height: 16,
              child: CircularProgressIndicator(
                strokeWidth: 2,
                valueColor: AlwaysStoppedAnimation<Color>(Color(0xFFD97706)),
              ),
            ),
            const SizedBox(width: 12),
            Text(
              'Acharya Vashistha is reading the Vedic planetary positions…',
              style: TextStyle(
                fontSize: 12.5,
                fontWeight: FontWeight.w600,
                color: isDark ? const Color(0xFFFDE68A) : const Color(0xFF92400E),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTurnBubble(BuildContext context, _AiTurn turn, bool isDark) {
    if (turn.mine) {
      return Align(
        alignment: Alignment.centerRight,
        child: Container(
          margin: const EdgeInsets.symmetric(vertical: 6),
          constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.82),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          decoration: BoxDecoration(
            gradient: CustomerTheme.saffronGradient,
            borderRadius: const BorderRadius.only(
              topLeft: Radius.circular(18),
              topRight: Radius.circular(18),
              bottomLeft: Radius.circular(18),
              bottomRight: Radius.circular(4),
            ),
            boxShadow: [
              BoxShadow(
                color: const Color(0xFFD97706).withValues(alpha: 0.25),
                blurRadius: 8,
                offset: const Offset(0, 3),
              ),
            ],
          ),
          child: Text(
            turn.text,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 14,
              height: 1.35,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
      );
    }

    // AI Turn (Acharya Vashistha)
    return Align(
      alignment: Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.symmetric(vertical: 8),
        constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.92),
        decoration: BoxDecoration(
          color: isDark ? const Color(0xFF1F1735) : Colors.white,
          borderRadius: const BorderRadius.only(
            topLeft: Radius.circular(4),
            topRight: Radius.circular(20),
            bottomLeft: Radius.circular(20),
            bottomRight: Radius.circular(20),
          ),
          border: Border.all(
            color: isDark ? const Color(0xFF4C1D95).withValues(alpha: 0.5) : const Color(0xFFFDE68A),
            width: 1.2,
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: isDark ? 0.3 : 0.05),
              blurRadius: 10,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Top author header
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
              decoration: BoxDecoration(
                color: isDark ? const Color(0xFF281E45) : const Color(0xFFFFFBEB),
                borderRadius: const BorderRadius.only(
                  topRight: Radius.circular(18),
                ),
              ),
              child: Row(
                children: [
                  const Text('🕉️', style: TextStyle(fontSize: 14)),
                  const SizedBox(width: 8),
                  Text(
                    'Acharya Vashistha',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w800,
                      color: isDark ? const Color(0xFFFDE68A) : const Color(0xFF92400E),
                    ),
                  ),
                  const Spacer(),
                  IconButton(
                    icon: const Icon(Icons.copy_rounded, size: 15),
                    tooltip: 'Copy Guidance',
                    padding: EdgeInsets.zero,
                    constraints: const BoxConstraints(),
                    color: isDark ? Colors.white60 : Colors.black45,
                    onPressed: () => _copyToClipboard(turn.text),
                  ),
                ],
              ),
            ),

            // Message text body
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 12),
              child: _renderFormattedSpiritualText(turn.text, isDark),
            ),

            // Deep Action CTAs (Consult Human Pandit / Book Puja)
            Padding(
              padding: const EdgeInsets.fromLTRB(12, 0, 12, 12),
              child: Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 8),
                        side: const BorderSide(color: Color(0xFFD97706)),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                      ),
                      onPressed: () {
                        Navigator.of(context).pop();
                        MainShellScope.of(context)?.selectTab(3); // Astrologers tab
                      },
                      icon: const Icon(Icons.phone_in_talk_rounded, size: 15, color: Color(0xFFD97706)),
                      label: const Text('Live Astrologer', style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.bold, color: Color(0xFFD97706))),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF92400E),
                        padding: const EdgeInsets.symmetric(vertical: 8),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                      ),
                      onPressed: () {
                        Navigator.of(context).pop();
                        MainShellScope.of(context)?.selectTab(2); // Puja tab
                      },
                      icon: const Icon(Icons.local_fire_department_rounded, size: 15, color: Colors.white),
                      label: const Text('Book Puja Upay', style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.bold, color: Colors.white)),
                    ),
                  ),
                ],
              ),
            ),

            // Dynamic follow-up chips if present
            if (turn.suggestions != null && turn.suggestions!.isNotEmpty)
              Container(
                padding: const EdgeInsets.fromLTRB(12, 6, 12, 12),
                child: Wrap(
                  spacing: 6,
                  runSpacing: 6,
                  children: turn.suggestions!.map((s) {
                    return InkWell(
                      onTap: () => _ask(s),
                      borderRadius: BorderRadius.circular(16),
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                        decoration: BoxDecoration(
                          color: isDark ? const Color(0xFF281E45) : const Color(0xFFF3F4F6),
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: isDark ? Colors.white12 : Colors.black12),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const Icon(Icons.chat_bubble_outline_rounded, size: 11, color: Color(0xFFD97706)),
                            const SizedBox(width: 5),
                            Text(
                              s,
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w600,
                                color: isDark ? Colors.white70 : Colors.black87,
                              ),
                            ),
                          ],
                        ),
                      ),
                    );
                  }).toList(),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _renderFormattedSpiritualText(String text, bool isDark) {
    final lines = text.split('\n');
    final List<Widget> list = [];

    for (final raw in lines) {
      final line = raw.trim();
      if (line.isEmpty) {
        list.add(const SizedBox(height: 6));
        continue;
      }

      if (line.startsWith('### ') || line.startsWith('## ') || line.startsWith('# ')) {
        final title = line.replaceFirst(RegExp(r'^#+\s*'), '');
        list.add(Padding(
          padding: const EdgeInsets.only(top: 8, bottom: 4),
          child: Text(
            title,
            style: TextStyle(
              fontSize: 14.5,
              fontWeight: FontWeight.w800,
              color: isDark ? const Color(0xFFFDE68A) : const Color(0xFF92400E),
            ),
          ),
        ));
      } else if (line.startsWith('1.') || line.startsWith('2.') || line.startsWith('3.') || line.startsWith('4.')) {
        list.add(Padding(
          padding: const EdgeInsets.symmetric(vertical: 3),
          child: Text(
            line,
            style: TextStyle(
              fontSize: 13.5,
              fontWeight: FontWeight.w600,
              color: isDark ? Colors.white : Colors.black87,
              height: 1.4,
            ),
          ),
        ));
      } else {
        list.add(Padding(
          padding: const EdgeInsets.symmetric(vertical: 2),
          child: Text(
            line.replaceAll('**', ''),
            style: TextStyle(
              fontSize: 13.5,
              color: isDark ? Colors.white70 : const Color(0xFF374151),
              height: 1.45,
            ),
          ),
        ));
      }
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: list,
    );
  }
}
