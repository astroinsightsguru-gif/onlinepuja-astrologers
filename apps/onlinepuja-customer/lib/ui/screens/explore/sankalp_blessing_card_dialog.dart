import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../theme/customer_theme.dart';

class DeityBlessingOption {
  final String deityName;
  final String temple;
  final String emoji;
  final String shloka;
  final String blessingWish;
  final List<Color> colors;

  const DeityBlessingOption({
    required this.deityName,
    required this.temple,
    required this.emoji,
    required this.shloka,
    required this.blessingWish,
    required this.colors,
  });
}

/// WhatsApp Sankalp Blessing Card Generator
class SankalpBlessingCardDialog extends StatefulWidget {
  const SankalpBlessingCardDialog({
    super.key,
    this.initialName,
    this.initialGotra,
  });

  final String? initialName;
  final String? initialGotra;

  static void show(BuildContext context, {String? defaultName, String? defaultGotra}) {
    showDialog(
      context: context,
      builder: (_) => SankalpBlessingCardDialog(
        initialName: defaultName,
        initialGotra: defaultGotra,
      ),
    );
  }

  @override
  State<SankalpBlessingCardDialog> createState() => _SankalpBlessingCardDialogState();
}

class _SankalpBlessingCardDialogState extends State<SankalpBlessingCardDialog> {
  late final TextEditingController _nameCtrl;
  late final TextEditingController _gotraCtrl;

  static const List<DeityBlessingOption> _deityOptions = [
    DeityBlessingOption(
      deityName: 'Lord Shiva (Mahadev)',
      temple: 'Kashi Vishwanath Jyotirlinga',
      emoji: '🔱',
      shloka: 'ॐ नमः शम्भवाय च मयोभवाय च नमः शङ्कराय च मयस्कराय च नमः शिवाय च शिवतराय च॥',
      blessingWish: 'May Lord Shiva bless you and your family with boundless longevity, profound mental peace, and protection from all negative energies.',
      colors: [Color(0xFF0F172A), Color(0xFF1E3A8A)],
    ),
    DeityBlessingOption(
      deityName: 'Lord Hanuman (Sankat Mochan)',
      temple: 'Ayodhya Dham Mandir',
      emoji: '🚩',
      shloka: 'पवनतनय संकट हरन मंगल मूरति रूप। राम लखन सीता सहित हृदय बसहु सुर भूप॥',
      blessingWish: 'May Mahaveer Hanuman eradicate all obstacles, grant fearless courage, and protect your loved ones from illness and adversity.',
      colors: [Color(0xFF9A3412), Color(0xFFEA580C)],
    ),
    DeityBlessingOption(
      deityName: 'Maa Mahalakshmi',
      temple: 'Shri Tirupati Balaji Sanctum',
      emoji: '🪷',
      shloka: 'ॐ सर्वाबाधा विनिर्मुक्तो, धनधान्यसुतान्वितः। मनुष्यो मत्प्रसादेन भविष्यति न संशयः॥',
      blessingWish: 'May Goddess Mahalakshmi fill your home with enduring prosperity, health, and auspicious golden abundance.',
      colors: [Color(0xFF831843), Color(0xFFDB2777)],
    ),
    DeityBlessingOption(
      deityName: 'Lord Ganesha (Vighnaharta)',
      temple: 'Siddhivinayak Ganapati Temple',
      emoji: '🐘',
      shloka: 'वक्रतुण्ड महाकाय सूर्यकोटि समप्रभ। निर्विघ्नं कुरु मे देव सर्वकार्येषु सर्वदा॥',
      blessingWish: 'May Sri Ganesha remove all hurdles from your career, grant sweet harmony, and inaugurate every auspicious new beginning.',
      colors: [Color(0xFFB45309), Color(0xFFD97706)],
    ),
  ];

  late DeityBlessingOption _selectedDeity;

  @override
  void initState() {
    super.initState();
    _nameCtrl = TextEditingController(text: widget.initialName ?? 'Devotee');
    _gotraCtrl = TextEditingController(text: widget.initialGotra ?? 'Kashyap');
    _selectedDeity = _deityOptions.first;
  }

  @override
  void dispose() {
    _nameCtrl.dispose();
    _gotraCtrl.dispose();
    super.dispose();
  }

  String _generateShareText() {
    final name = _nameCtrl.text.trim().isEmpty ? 'Devotee' : _nameCtrl.text.trim();
    final gotra = _gotraCtrl.text.trim().isEmpty ? 'Kashyap' : _gotraCtrl.text.trim();
    final d = _selectedDeity;
    final date = DateFormat('dd MMMM yyyy').format(DateTime.now());

    final buf = StringBuffer();
    buf.writeln('🕉️ *DIVINE TEMPLE SANKALP & BLESSING CARD* 🕉️');
    buf.writeln('━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    buf.writeln('${d.emoji} *Deity:* ${d.deityName}');
    buf.writeln('🏛️ *Kshetra:* ${d.temple}');
    buf.writeln('📅 *Auspicious Date:* $date');
    buf.writeln('━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    buf.writeln('👤 *Devotee (यजमान):* $name');
    buf.writeln('🌿 *Gotra (गोत्र):* $gotra');
    buf.writeln('\n✨ *Sacred Vedic Shloka:*');
    buf.writeln(d.shloka);
    buf.writeln('\n🪔 *Temple Ashirwad:*');
    buf.writeln(d.blessingWish);
    buf.writeln('━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    buf.writeln('🕊️ _Book authenticated Vedic Temple Pujas and receive consecrated Prasad at home:_');
    buf.writeln('👉 https://onlinepuja.live');

    return buf.toString();
  }

  Future<void> _shareOnWhatsApp() async {
    final text = _generateShareText();
    final url = Uri.parse('https://api.whatsapp.com/send?text=${Uri.encodeComponent(text)}');
    try {
      await launchUrl(url, mode: LaunchMode.externalApplication);
      if (mounted) Navigator.pop(context);
    } catch (_) {
      if (mounted) {
        Clipboard.setData(ClipboardData(text: text));
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Blessing text copied to clipboard!')),
        );
      }
    }
  }

  void _copyToClipboard() {
    Clipboard.setData(ClipboardData(text: _generateShareText()));
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Divine blessing card text copied!')),
    );
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final d = _selectedDeity;
    final date = DateFormat('dd MMMM yyyy').format(DateTime.now());

    return Dialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
      backgroundColor: isDark ? const Color(0xFF18130E) : Colors.white,
      insetPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
      child: ConstrainedBox(
        constraints: const BoxConstraints(maxWidth: 440),
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Header
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      gradient: CustomerTheme.goldGradient,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(Icons.card_giftcard_rounded, color: Colors.white, size: 20),
                  ),
                  const SizedBox(width: 10),
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Sankalp Blessing Card',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900),
                        ),
                        Text(
                          'Generate & Share Divine Temple Ashirwad',
                          style: TextStyle(fontSize: 11, color: Colors.grey),
                        ),
                      ],
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close_rounded),
                    onPressed: () => Navigator.pop(context),
                  ),
                ],
              ),
              const SizedBox(height: 14),

              // Devotee Name & Gotra Inputs
              Row(
                children: [
                  Expanded(
                    flex: 3,
                    child: TextField(
                      controller: _nameCtrl,
                      onChanged: (_) => setState(() {}),
                      decoration: const InputDecoration(
                        labelText: 'Devotee Name',
                        prefixIcon: Icon(Icons.person_outline, size: 18),
                        isDense: true,
                      ),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    flex: 2,
                    child: TextField(
                      controller: _gotraCtrl,
                      onChanged: (_) => setState(() {}),
                      decoration: const InputDecoration(
                        labelText: 'Gotra',
                        prefixIcon: Icon(Icons.spa_outlined, size: 18),
                        isDense: true,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),

              // Deity Selector Chips
              SizedBox(
                height: 38,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  itemCount: _deityOptions.length,
                  itemBuilder: (ctx, idx) {
                    final opt = _deityOptions[idx];
                    final isSel = opt.deityName == d.deityName;
                    return Padding(
                      padding: const EdgeInsets.only(right: 6),
                      child: ChoiceChip(
                        selected: isSel,
                        label: Text('${opt.emoji} ${opt.deityName.split(' ').first}'),
                        labelStyle: TextStyle(
                          fontSize: 11,
                          fontWeight: isSel ? FontWeight.bold : FontWeight.normal,
                          color: isSel ? Colors.white : null,
                        ),
                        selectedColor: const Color(0xFFD97706),
                        onSelected: (_) => setState(() => _selectedDeity = opt),
                      ),
                    );
                  },
                ),
              ),

              const SizedBox(height: 16),

              // Auspicious Visual Blessing Card (Preview)
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: d.colors,
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: const Color(0xFFFDE68A), width: 1.8),
                  boxShadow: [
                    BoxShadow(
                      color: d.colors.first.withValues(alpha: 0.35),
                      blurRadius: 18,
                      offset: const Offset(0, 6),
                    ),
                  ],
                ),
                child: Column(
                  children: [
                    // Top Om & Temple Banner
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('ॐ', style: TextStyle(color: Color(0xFFFDE68A), fontSize: 24, fontWeight: FontWeight.bold)),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                          decoration: BoxDecoration(
                            color: Colors.white.withValues(alpha: 0.18),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Text(
                            d.temple,
                            style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                          ),
                        ),
                        const Text('卐', style: TextStyle(color: Color(0xFFFDE68A), fontSize: 22, fontWeight: FontWeight.bold)),
                      ],
                    ),
                    const SizedBox(height: 12),

                    Text(
                      d.emoji,
                      style: const TextStyle(fontSize: 44),
                    ),
                    const SizedBox(height: 8),

                    Text(
                      d.deityName.toUpperCase(),
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                        color: Color(0xFFFDE68A),
                        fontSize: 15,
                        fontWeight: FontWeight.w900,
                        letterSpacing: 1.2,
                      ),
                    ),
                    const SizedBox(height: 6),

                    // Shloka
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                      decoration: BoxDecoration(
                        color: Colors.black.withValues(alpha: 0.25),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Text(
                        d.shloka,
                        textAlign: TextAlign.center,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 11,
                          fontStyle: FontStyle.italic,
                          height: 1.35,
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),

                    // Devotee Sankalp Badge
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.15),
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: Colors.white24),
                      ),
                      child: Column(
                        children: [
                          Text(
                            'यजमान: ${_nameCtrl.text.trim().isEmpty ? 'Devotee' : _nameCtrl.text.trim()}',
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 14,
                              fontWeight: FontWeight.w900,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            'Gotra: ${_gotraCtrl.text.trim().isEmpty ? 'Kashyap' : _gotraCtrl.text.trim()} · $date',
                            style: const TextStyle(color: Colors.white70, fontSize: 11),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 10),

                    // Ashirwad Blessing
                    Text(
                      d.blessingWish,
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 11,
                        height: 1.35,
                      ),
                    ),
                    const SizedBox(height: 10),

                    const Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.verified_rounded, color: Color(0xFFFDE68A), size: 14),
                        SizedBox(width: 4),
                        Text(
                          'OnlinePuja.live Sanctum Certified',
                          style: TextStyle(color: Color(0xFFFDE68A), fontSize: 10, fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 18),

              // Action Buttons
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      icon: const Icon(Icons.copy_rounded, size: 16),
                      label: const Text('Copy Text', style: TextStyle(fontSize: 12)),
                      onPressed: _copyToClipboard,
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: FilledButton.icon(
                      icon: const Icon(Icons.share_rounded, size: 16),
                      label: const Text('WhatsApp', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                      style: FilledButton.styleFrom(backgroundColor: const Color(0xFF25D366)),
                      onPressed: _shareOnWhatsApp,
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
