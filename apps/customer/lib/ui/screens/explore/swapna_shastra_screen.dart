import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

/// Swapna Shastra & Divine Omen Interpreter.
/// Interprets spiritual dreams according to ancient Vedic scriptures.
class SwapnaShastraScreen extends StatefulWidget {
  const SwapnaShastraScreen({super.key});

  static const route = '/swapna-shastra';

  @override
  State<SwapnaShastraScreen> createState() => _SwapnaShastraScreenState();
}

class _SwapnaShastraScreenState extends State<SwapnaShastraScreen> {
  final _controller = TextEditingController();
  bool _loading = false;
  String? _interpretation;

  final List<String> _commonDreams = [
    '🐍 Seeing a Snake / Nag Devata',
    '🛕 Visiting a Holy Temple or Shivling',
    '🌊 Bathing in Sacred River Ganga',
    '🐘 Seeing a Royal Elephant or Cow',
    '🕊️ Seeing Ancestors (Pitras) Blessing You',
    '🔥 Seeing a Sacred Havan / Agni Kund',
  ];

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> _interpretDream(String text) async {
    final dream = text.trim();
    if (dream.isEmpty) return;
    _controller.text = dream;

    setState(() {
      _loading = true;
      _interpretation = null;
    });

    final prompt = 'Act as an expert in authentic ancient Vedic Swapna Shastra (dream interpretation) and Jyotish Shakun Shastra. '
        'A devotee had this dream:\n"$dream"\n\n'
        'Provide an authentic, culturally respectful, and inspiring Vedic interpretation with these 3 sections:\n'
        '1. SWAPNA MEANING: Is this dream Shubh (auspicious) or indicating a spiritual warning?\n'
        '2. PLANETARY & SPIRITUAL SIGNIFICANCE: Which deities or planets are connected to this omen?\n'
        '3. PRESCRIBED VEDIC REMEDY / PRAYER: Simple morning ritual, mantra, or charitable act (e.g. offering water to Surya, feeding a cow, chanting Om Namah Shivaya). Keep it warm, concise, and uplifting.';

    try {
      final res = await ApiClient.instance.post('/ask-master', body: {
        'message': prompt,
      });

      String reply = '';
      if (res is Map<String, dynamic>) {
        final rl = res['recordList'];
        if (rl is Map<String, dynamic>) {
          reply = rl['reply']?.toString() ?? rl['message']?.toString() ?? '';
        } else if (res['reply'] != null) {
          reply = res['reply'].toString();
        } else if (res['message'] != null && res['status'] == 200) {
          reply = res['message'].toString();
        }
      }

      if (mounted) {
        setState(() {
          _interpretation = reply.isNotEmpty ? reply : _fallbackInterpretation(dream);
          _loading = false;
        });
      }
    } catch (_) {
      if (mounted) {
        setState(() {
          _interpretation = _fallbackInterpretation(dream);
          _loading = false;
        });
      }
    }
  }

  String _fallbackInterpretation(String dream) {
    return '1. SWAPNA MEANING: Highly Auspicious (Shubh Sanket). In Swapna Shastra, sacred symbols like temples, water, and serpents represent spiritual elevation and clearing of past karmas.\n\n'
        '2. SPIRITUAL SIGNIFICANCE: Governed by the energies of Lord Shiva and Jupiter (Guru). Indicates divine protection and success in upcoming endeavors.\n\n'
        '3. PRESCRIBED PRAYER: Light a ghee lamp in your home temple this morning. Chant "Om Namah Shivaya" 21 times and offer fresh water to a Tulsi plant or Peepal tree for continuous blessings.';
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Swapna Shastra (Dream Interpreter)'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Hero Card
            Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [AppTheme.brandDeep, AppTheme.brandDeep.withValues(alpha: 0.85)],
                ),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: AppTheme.brandSaffron.withValues(alpha: 0.4)),
              ),
              child: Row(
                children: [
                  Container(
                    width: 50,
                    height: 50,
                    decoration: BoxDecoration(
                      color: AppTheme.brandSaffron.withValues(alpha: 0.2),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: const Center(child: Text('🌙', style: TextStyle(fontSize: 24))),
                  ),
                  const SizedBox(width: 14),
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Vedic Dream Analysis', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                        SizedBox(height: 3),
                        Text(
                          'Dreams are soul messages. Discover their auspicious omen according to sacred scriptures.',
                          style: TextStyle(color: Colors.white70, fontSize: 12),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 22),

            Text('Describe What You Saw in Your Dream:', style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            TextField(
              controller: _controller,
              maxLines: 3,
              decoration: InputDecoration(
                hintText: 'e.g., I saw myself offering flowers at an ancient Shiva temple near a river…',
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                filled: true,
                fillColor: scheme.surfaceContainerHighest.withValues(alpha: 0.3),
              ),
            ),
            const SizedBox(height: 12),

            SizedBox(
              width: double.infinity,
              height: 48,
              child: FilledButton.icon(
                style: FilledButton.styleFrom(
                  backgroundColor: AppTheme.brandSaffron,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                ),
                onPressed: _loading ? null : () => _interpretDream(_controller.text),
                icon: _loading
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                      )
                    : const Icon(Icons.auto_awesome, size: 20),
                label: Text(_loading ? 'Decoding Dream Omens…' : 'Interpret My Dream'),
              ),
            ),
            const SizedBox(height: 20),

            Text('Or Select Common Sacred Dreams:', style: Theme.of(context).textTheme.bodySmall?.copyWith(color: scheme.outline)),
            const SizedBox(height: 10),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: _commonDreams.map((d) {
                return ActionChip(
                  label: Text(d, style: const TextStyle(fontSize: 12)),
                  backgroundColor: scheme.surfaceContainerHighest.withValues(alpha: 0.4),
                  onPressed: _loading ? null : () => _interpretDream(d),
                );
              }).toList(),
            ),
            const SizedBox(height: 24),

            if (_interpretation != null)
              Container(
                padding: const EdgeInsets.all(18),
                decoration: BoxDecoration(
                  color: scheme.surface,
                  borderRadius: BorderRadius.circular(18),
                  border: Border.all(color: AppTheme.brandGold.withValues(alpha: 0.5), width: 1.5),
                  boxShadow: [
                    BoxShadow(
                      color: AppTheme.brandGold.withValues(alpha: 0.1),
                      blurRadius: 14,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        const Icon(Icons.psychology_alt_rounded, color: AppTheme.brandGold, size: 22),
                        const SizedBox(width: 8),
                        Text(
                          'Swapna Shastra Interpretation',
                          style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                    const Divider(height: 20),
                    Text(
                      _interpretation!,
                      style: const TextStyle(fontSize: 13.5, height: 1.5),
                    ),
                  ],
                ),
              ),
          ],
        ),
      ),
    );
  }
}
