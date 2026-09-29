import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:op_shared/op_shared.dart';

/// Represents a sacred Vedic mantra with deity and benefits.
class MantraItem {
  final String title;
  final String deity;
  final String sanskrit;
  final String meaning;
  final String benefit;

  const MantraItem({
    required this.title,
    required this.deity,
    required this.sanskrit,
    required this.meaning,
    required this.benefit,
  });
}

/// Digital Rudraksha Japa Mala screen.
/// 108 beads counter with realistic haptic clicks, Sanskrit mantras, and streak tracking.
class JapaMalaScreen extends StatefulWidget {
  const JapaMalaScreen({super.key});

  static const route = '/japa-mala';

  @override
  State<JapaMalaScreen> createState() => _JapaMalaScreenState();
}

class _JapaMalaScreenState extends State<JapaMalaScreen> {
  int _currentCount = 0;
  int _malasCompleted = 0;
  final int _targetMalas = 1;
  late MantraItem _selectedMantra;

  static const List<MantraItem> _mantras = [
    MantraItem(
      title: 'Maha Mrityunjaya Mantra',
      deity: 'Lord Shiva (Rudra)',
      sanskrit: 'ॐ त्र्यम्बकं यजामहे सुगन्धिं पुष्टिवर्धनम्।\nउर्वारुकमिव बन्धनान्मृत्योर्मुक्षीय माऽमृतात्॥',
      meaning: 'We worship the Three-eyed Lord Shiva who nourishes all beings. May He liberate us from death and bondages unto immortality.',
      benefit: 'Protection from untimely death, chronic illness, and mental anxieties.',
    ),
    MantraItem(
      title: 'Gayatri Mantra',
      deity: 'Maa Savitri & Surya Dev',
      sanskrit: 'ॐ भूर्भुवः स्वः तत्सवितुर्वरेण्यं\nभर्गो देवस्य धीमहि धियो यो नः प्रचोदयात्॥',
      meaning: 'We meditate on the divine light of the Sun. May that radiant supreme consciousness enlighten our minds and intellect.',
      benefit: 'Wisdom, mental clarity, spiritual awakening, and inner peace.',
    ),
    MantraItem(
      title: 'Shiva Panchakshari Mantra',
      deity: 'Lord Shiva',
      sanskrit: 'ॐ नमः शिवाय',
      meaning: 'I bow with reverence to the Auspicious Supreme Lord Shiva.',
      benefit: 'Purification of five elements in the body, peace of mind, and inner stillness.',
    ),
    MantraItem(
      title: 'Hare Krishna Mahamantra',
      deity: 'Lord Krishna & Radha Rani',
      sanskrit: 'हरे कृष्ण हरे कृष्ण कृष्ण कृष्ण हरे हरे।\nहरे राम हरे राम राम राम हरे हरे॥',
      meaning: 'O Divine Energy (Hare), O Supreme Attractive Lord (Krishna), O Source of all Joy (Rama), please engage me in Your service.',
      benefit: 'Supreme bliss, cleansing of past karmas, and liberation.',
    ),
    MantraItem(
      title: 'Hanuman Mool Mantra',
      deity: 'Lord Hanuman',
      sanskrit: 'ॐ हं हनुमते रुद्रात्मकाय हुं फट्॥',
      meaning: 'Salutations to the mighty incarnation of Rudra, Lord Hanuman, who destroys all negative energies and grants courage.',
      benefit: 'Fearlessness, victory over enemies, and removal of evil planetary influences.',
    ),
  ];

  @override
  void initState() {
    super.initState();
    _selectedMantra = _mantras.first;
  }

  void _incrementBead() {
    HapticFeedback.lightImpact();
    setState(() {
      _currentCount++;
      if (_currentCount >= 108) {
        _currentCount = 0;
        _malasCompleted++;
        HapticFeedback.heavyImpact();
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            backgroundColor: AppTheme.brandDeep,
            content: Text('🔔 108 Japa Completed! Total Malas: $_malasCompleted. Har Har Mahadev!'),
          ),
        );
      }
    });
  }

  void _resetCounter() {
    setState(() {
      _currentCount = 0;
    });
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Digital Rudraksha Mala'),
        actions: [
          IconButton(
            tooltip: 'Reset Count',
            icon: const Icon(Icons.refresh_rounded),
            onPressed: () {
              showDialog(
                context: context,
                builder: (ctx) => AlertDialog(
                  title: const Text('Reset Mala Counter?'),
                  content: const Text('This will reset your current bead count to 0.'),
                  actions: [
                    TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
                    FilledButton(
                      onPressed: () {
                        _resetCounter();
                        Navigator.pop(ctx);
                      },
                      child: const Text('Reset'),
                    ),
                  ],
                ),
              );
            },
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            // Daily Streak & Target Card
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 14),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [AppTheme.brandDeep, AppTheme.brandDeep.withValues(alpha: 0.85)],
                ),
                borderRadius: BorderRadius.circular(18),
                border: Border.all(color: AppTheme.brandSaffron.withValues(alpha: 0.4)),
              ),
              child: Row(
                children: [
                  const Text('🔥', style: TextStyle(fontSize: 26)),
                  const SizedBox(width: 12),
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Daily Sadhana Streak', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14)),
                        Text('7 Days Continuous Chanting', style: TextStyle(color: Colors.white70, fontSize: 12)),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: AppTheme.brandSaffron,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      '$_malasCompleted / $_targetMalas Mala',
                      style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Main Interactive Rudraksha Bead / Clicker Button
            GestureDetector(
              onTap: _incrementBead,
              child: Container(
                width: 240,
                height: 240,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: RadialGradient(
                    colors: [
                      AppTheme.brandSaffron,
                      AppTheme.brandDeep,
                      const Color(0xFF2C0B0E),
                    ],
                    radius: 0.85,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: AppTheme.brandSaffron.withValues(alpha: 0.35),
                      blurRadius: 30,
                      spreadRadius: 4,
                      offset: const Offset(0, 8),
                    ),
                  ],
                ),
                child: Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Text('📿', style: TextStyle(fontSize: 32)),
                      const SizedBox(height: 6),
                      Text(
                        '$_currentCount',
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 60,
                          fontWeight: FontWeight.bold,
                          letterSpacing: 2,
                        ),
                      ),
                      const Text(
                        'OF 108 BEADS',
                        style: TextStyle(
                          color: Colors.white70,
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          letterSpacing: 1.5,
                        ),
                      ),
                      const SizedBox(height: 6),
                      const Text(
                        'Tap Anywhere to Chant',
                        style: TextStyle(
                          color: AppTheme.brandGold,
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
            const SizedBox(height: 24),

            // Active Mantra Card
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                color: scheme.surface,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: AppTheme.brandGold.withValues(alpha: 0.4)),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.04),
                    blurRadius: 10,
                  ),
                ],
              ),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Icon(Icons.auto_awesome, color: AppTheme.brandGold, size: 18),
                      const SizedBox(width: 8),
                      Text(
                        _selectedMantra.title,
                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                      ),
                    ],
                  ),
                  const SizedBox(height: 10),
                  Text(
                    _selectedMantra.sanskrit,
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                      color: AppTheme.brandDeep,
                      height: 1.5,
                    ),
                  ),
                  const SizedBox(height: 12),
                  const Divider(),
                  const SizedBox(height: 6),
                  Text(
                    _selectedMantra.meaning,
                    textAlign: TextAlign.center,
                    style: TextStyle(fontSize: 12, color: scheme.outline, height: 1.4),
                  ),
                  const SizedBox(height: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.green.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      '✨ Benefit: ${_selectedMantra.benefit}',
                      textAlign: TextAlign.center,
                      style: const TextStyle(color: Colors.green, fontSize: 11, fontWeight: FontWeight.bold),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Mantra Selector
            Align(
              alignment: Alignment.centerLeft,
              child: Text(
                'Select Divine Mantra to Japa:',
                style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold),
              ),
            ),
            const SizedBox(height: 10),
            ListView.separated(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: _mantras.length,
              separatorBuilder: (context, index) => const SizedBox(height: 8),
              itemBuilder: (context, i) {
                final m = _mantras[i];
                final isSelected = m.title == _selectedMantra.title;

                return InkWell(
                  borderRadius: BorderRadius.circular(12),
                  onTap: () {
                    setState(() {
                      _selectedMantra = m;
                    });
                  },
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                    decoration: BoxDecoration(
                      color: isSelected
                          ? AppTheme.brandSaffron.withValues(alpha: 0.12)
                          : scheme.surfaceContainerHighest.withValues(alpha: 0.3),
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: isSelected ? AppTheme.brandSaffron : scheme.outline.withValues(alpha: 0.2),
                        width: isSelected ? 1.5 : 1,
                      ),
                    ),
                    child: Row(
                      children: [
                        Icon(
                          isSelected ? Icons.radio_button_checked : Icons.radio_button_off,
                          color: isSelected ? AppTheme.brandSaffron : scheme.outline,
                          size: 20,
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(m.title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                              Text(m.deity, style: TextStyle(color: scheme.outline, fontSize: 11)),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ],
        ),
      ),
    );
  }
}
