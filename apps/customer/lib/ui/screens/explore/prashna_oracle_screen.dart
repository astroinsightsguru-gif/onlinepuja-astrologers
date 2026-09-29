import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

/// Instant Prashna Kundli & Spiritual Oracle screen.
/// Answers immediate life questions using Vedic Horary astrology & planetary alignments.
class PrashnaOracleScreen extends StatefulWidget {
  const PrashnaOracleScreen({super.key});

  static const route = '/prashna-oracle';

  @override
  State<PrashnaOracleScreen> createState() => _PrashnaOracleScreenState();
}

class _PrashnaOracleScreenState extends State<PrashnaOracleScreen> {
  final _controller = TextEditingController();
  bool _loading = false;
  Map<String, dynamic>? _result;

  final List<String> _quickQuestions = [
    '💼 Will my career/job improve soon?',
    '💍 When will I meet my life partner?',
    '✈️ Is foreign travel/visa favorable now?',
    '💰 When will my financial blockages clear?',
    '🩺 What remedies will protect my health?',
  ];

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> _askOracle(String question) async {
    final q = question.trim();
    if (q.isEmpty) return;
    _controller.text = q;

    setState(() {
      _loading = true;
      _result = null;
    });

    final now = DateTime.now();
    final timeStr = '${now.hour.toString().padLeft(2, '0')}:${now.minute.toString().padLeft(2, '0')}';
    final dateStr = '${now.day}/${now.month}/${now.year}';

    final prompt = 'Act as an authentic Vedic Horary Astrologer (Prashna Shastra expert). '
        'A devotee is asking an urgent Prashna query at $dateStr $timeStr.\n'
        'Question: "$q"\n\n'
        'Provide a clear, respectful, and authentic Vedic Prashna reading with the following sections:\n'
        '1. PRASHNA VERDICT: State whether the outcome is Favorable, Favorable with Effort, or Requires Patience.\n'
        '2. PLANETARY ALIGNMENT: Explain the horary planetary ruler and energy of the current moment.\n'
        '3. VEDIC REMEDY: Specific actionable spiritual remedy (e.g., chanting a mantra, offering to a deity, wearing an auspicious color). Keep it concise, inspiring, and culturally authentic.';

    try {
      final res = await ApiClient.instance.post('/ask-master', body: {
        'message': prompt,
      });

      String text = '';
      if (res is Map<String, dynamic>) {
        final rl = res['recordList'];
        if (rl is Map<String, dynamic>) {
          text = rl['reply']?.toString() ?? rl['message']?.toString() ?? '';
        } else if (res['reply'] != null) {
          text = res['reply'].toString();
        } else if (res['message'] != null && res['status'] == 200) {
          text = res['message'].toString();
        }
      }

      if (mounted) {
        setState(() {
          _result = {
            'text': text.isNotEmpty ? text : _fallbackReading(q),
            'timestamp': '$dateStr at $timeStr',
          };
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _result = {
            'text': _fallbackReading(q),
            'timestamp': '$dateStr at $timeStr',
          };
          _loading = false;
        });
      }
    }
  }

  String _fallbackReading(String q) {
    return '1. PRASHNA VERDICT: Favorable with Effort & Right Timing.\n\n'
        '2. PLANETARY ALIGNMENT: The current Prashna lagna is influenced by Jupiter and Mercury, indicating growth through clear communication, righteous action, and perseverance. Avoid rushing into decisions without prayerful reflection.\n\n'
        '3. VEDIC REMEDY: Chant the Gayatri Mantra 11 times every morning at sunrise. Offer water to the rising Sun (Surya Arghya) and light a sesame oil diya on Tuesday or Saturday for divine obstacles clearance.';
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Prashna Kundli & Oracle'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header Hero Banner
            Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [AppTheme.brandDeep, AppTheme.brandDeep.withValues(alpha: 0.85)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: AppTheme.brandSaffron.withValues(alpha: 0.3)),
              ),
              child: Row(
                children: [
                  Container(
                    width: 52,
                    height: 52,
                    decoration: BoxDecoration(
                      color: AppTheme.brandSaffron.withValues(alpha: 0.15),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: const Center(
                      child: Text('🔮', style: TextStyle(fontSize: 26)),
                    ),
                  ),
                  const SizedBox(width: 14),
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Instant Vedic Prashna',
                          style: TextStyle(color: Colors.white, fontSize: 17, fontWeight: FontWeight.bold),
                        ),
                        SizedBox(height: 4),
                        Text(
                          'Ask any immediate question. Horary charts reveal the cosmos’s answer at this exact moment.',
                          style: TextStyle(color: Colors.white70, fontSize: 12),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Input card
            Text('Enter Your Question', style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            TextField(
              controller: _controller,
              maxLines: 2,
              decoration: InputDecoration(
                hintText: 'e.g., Will my job interview tomorrow go well?',
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                filled: true,
                fillColor: scheme.surfaceContainerHighest.withValues(alpha: 0.3),
              ),
            ),
            const SizedBox(height: 12),

            // Ask Button
            SizedBox(
              width: double.infinity,
              height: 48,
              child: FilledButton.icon(
                style: FilledButton.styleFrom(
                  backgroundColor: AppTheme.brandSaffron,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                ),
                onPressed: _loading ? null : () => _askOracle(_controller.text),
                icon: _loading
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                      )
                    : const Icon(Icons.auto_awesome, size: 20),
                label: Text(_loading ? 'Calculating Planetary Alignment…' : 'Cast Prashna Chart & Reveal'),
              ),
            ),
            const SizedBox(height: 20),

            // Quick Questions
            Text('Or Tap a Popular Question:', style: Theme.of(context).textTheme.bodySmall?.copyWith(color: scheme.outline)),
            const SizedBox(height: 10),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: _quickQuestions.map((q) {
                return ActionChip(
                  label: Text(q, style: const TextStyle(fontSize: 12)),
                  backgroundColor: scheme.surfaceContainerHighest.withValues(alpha: 0.4),
                  onPressed: _loading ? null : () => _askOracle(q),
                );
              }).toList(),
            ),
            const SizedBox(height: 24),

            // Result Display
            if (_result != null) ...[
              Container(
                padding: const EdgeInsets.all(18),
                decoration: BoxDecoration(
                  color: scheme.surface,
                  borderRadius: BorderRadius.circular(18),
                  border: Border.all(color: AppTheme.brandGold.withValues(alpha: 0.5), width: 1.5),
                  boxShadow: [
                    BoxShadow(
                      color: AppTheme.brandGold.withValues(alpha: 0.1),
                      blurRadius: 16,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        const Icon(Icons.stars_rounded, color: AppTheme.brandGold, size: 22),
                        const SizedBox(width: 8),
                        Text(
                          'Prashna Shastra Verdict',
                          style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.bold),
                        ),
                        const Spacer(),
                        Text(
                          _result!['timestamp'] ?? '',
                          style: TextStyle(color: scheme.outline, fontSize: 11),
                        ),
                      ],
                    ),
                    const Divider(height: 24),
                    Text(
                      _result!['text'] ?? '',
                      style: const TextStyle(fontSize: 14, height: 1.5),
                    ),
                    const SizedBox(height: 16),
                    // Talk to Astrologer Banner
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: AppTheme.brandDeep,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.phone_in_talk, color: Colors.amberAccent, size: 20),
                          const SizedBox(width: 10),
                          const Expanded(
                            child: Text(
                              'Need deeper analysis?',
                              style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold),
                            ),
                          ),
                          TextButton(
                            style: TextButton.styleFrom(
                              foregroundColor: Colors.amberAccent,
                              visualDensity: VisualDensity.compact,
                            ),
                            onPressed: () => Navigator.of(context).pop(),
                            child: const Text('Consult Astrologer'),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
