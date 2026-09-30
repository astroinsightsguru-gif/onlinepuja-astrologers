import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../../theme/partner_theme.dart';
import '../../widgets/partner_widgets.dart';
import '../auth/login_screen.dart';

/// Partner profile: credentials, consultation rates, skills,
/// languages, bio edit, appearance theme, and support.
class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  static const route = '/profile';

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _bio = TextEditingController(
      text:
          'Gold medalist Vedic Astrologer & Vastu consultant with 12+ years of experience helping thousands find clarity in career, marriage, and spiritual path.');
  bool _saving = false;
  bool _initialized = false;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (!_initialized) {
      final u = context.read<PartnerSession>().user;
      _name.text = u?.name ?? '';
      _email.text = u?.email ?? '';
      _initialized = true;
    }
  }

  @override
  void dispose() {
    _name.dispose();
    _email.dispose();
    _bio.dispose();
    super.dispose();
  }

  Future<void> _save() async {
    final session = context.read<PartnerSession>();
    final u = session.user;
    if (u == null || !_formKey.currentState!.validate()) return;
    setState(() => _saving = true);
    try {
      u.name = _name.text.trim();
      u.email = _email.text.trim();
      await PartnerApi.instance.updateProfile(u);
      if (mounted) showSnack(context, 'Profile updated successfully');
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final session = context.watch<PartnerSession>();
    final u = session.user;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Astrologer Profile',
            style: TextStyle(fontWeight: FontWeight.w800, fontSize: 18)),
      ),
      body: ListView(
        padding: const EdgeInsets.fromLTRB(16, 10, 16, 32),
        children: [
          // Astrologer Hero Badge Card
          PartnerCard(
            gradient: dark
                ? PartnerTheme.darkCardGradient
                : const LinearGradient(
                    colors: [Color(0xFFFFF9F0), Color(0xFFFDEFD9)],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
            borderColor: PartnerTheme.gold.withValues(alpha: 0.5),
            padding: const EdgeInsets.all(20),
            child: Column(
              children: [
                AstrologerAvatar(
                  name: u?.displayName ?? 'Acharya',
                  radius: 40,
                  isOnline: session.chatStatus == 'Online',
                ),
                const SizedBox(height: 14),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      u?.displayName ?? 'Acharya Ji',
                      style: const TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                    const SizedBox(width: 6),
                    const Icon(Icons.verified_rounded,
                        color: PartnerTheme.emerald, size: 20),
                  ],
                ),
                const SizedBox(height: 3),
                Text(
                  'Verified Vedic Acharya · ID: #ASTRO-${u?.id ?? 108}',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: dark ? Colors.white60 : Colors.black54,
                  ),
                ),
                const SizedBox(height: 16),

                // Credentials Stat Row
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                  decoration: BoxDecoration(
                    color: dark ? Colors.black26 : Colors.white.withValues(alpha: 0.7),
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: PartnerTheme.gold.withValues(alpha: 0.3),
                    ),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceAround,
                    children: [
                      _credItem('4.9 ★', '1,420+ Reviews'),
                      Container(
                          width: 1,
                          height: 24,
                          color: Colors.grey.withValues(alpha: 0.3)),
                      _credItem('12+ Yrs', 'Experience'),
                      Container(
                          width: 1,
                          height: 24,
                          color: Colors.grey.withValues(alpha: 0.3)),
                      _credItem('8,500+', 'Devotees Served'),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 18),

          // Rates & Pricing Strip
          const Text(
            'Consultation Tariff Rates',
            style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              _rateCard('Audio Call', '₹35/min', Icons.call_rounded,
                  PartnerTheme.emerald, dark),
              const SizedBox(width: 8),
              _rateCard('Video Call', '₹50/min', Icons.videocam_rounded,
                  PartnerTheme.purple, dark),
              const SizedBox(width: 8),
              _rateCard('Chat Session', '₹25/min', Icons.chat_bubble_rounded,
                  PartnerTheme.saffron, dark),
            ],
          ),
          const SizedBox(height: 18),

          // Expertise & Specialization Tags
          PartnerCard(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Expertise & Skills',
                  style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 10),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: [
                    'Vedic Astrology',
                    'Kundli Milan',
                    'Vastu Shastra',
                    'Numerology',
                    'Prashna Kundli',
                    'Palmistry',
                    'Gemology',
                  ].map((s) {
                    return Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 5),
                      decoration: BoxDecoration(
                        color: PartnerTheme.saffron.withValues(alpha: 0.12),
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(
                          color: PartnerTheme.saffron.withValues(alpha: 0.3),
                        ),
                      ),
                      child: Text(
                        s,
                        style: const TextStyle(
                          fontSize: 11.5,
                          fontWeight: FontWeight.w700,
                          color: PartnerTheme.saffron,
                        ),
                      ),
                    );
                  }).toList(),
                ),
                const SizedBox(height: 14),
                const Text(
                  'Spoken Languages',
                  style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 10),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: ['Hindi', 'English', 'Sanskrit', 'Gujarati'].map((l) {
                    return Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 5),
                      decoration: BoxDecoration(
                        color: PartnerTheme.gold.withValues(alpha: 0.12),
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(
                          color: PartnerTheme.gold.withValues(alpha: 0.3),
                        ),
                      ),
                      child: Text(
                        l,
                        style: const TextStyle(
                          fontSize: 11.5,
                          fontWeight: FontWeight.w700,
                          color: PartnerTheme.goldDark,
                        ),
                      ),
                    );
                  }).toList(),
                ),
              ],
            ),
          ),
          const SizedBox(height: 18),

          // Edit Profile Form
          PartnerCard(
            padding: const EdgeInsets.all(18),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Profile Details & Bio',
                    style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
                  ),
                  const SizedBox(height: 14),
                  TextFormField(
                    controller: _name,
                    decoration: const InputDecoration(
                      labelText: 'Full Display Name',
                      prefixIcon: Icon(Icons.person_outline_rounded),
                    ),
                    validator: (v) => (v == null || v.trim().isEmpty)
                        ? 'Enter your name'
                        : null,
                  ),
                  const SizedBox(height: 12),
                  TextFormField(
                    controller: _email,
                    keyboardType: TextInputType.emailAddress,
                    decoration: const InputDecoration(
                      labelText: 'Registered Email',
                      prefixIcon: Icon(Icons.email_outlined),
                    ),
                    validator: (v) =>
                        (v != null && v.isNotEmpty && !v.contains('@'))
                            ? 'Enter a valid email'
                            : null,
                  ),
                  const SizedBox(height: 12),
                  TextFormField(
                    controller: _bio,
                    maxLines: 3,
                    decoration: const InputDecoration(
                      labelText: 'About Acharya / Bio',
                      prefixIcon: Icon(Icons.edit_note_rounded),
                    ),
                  ),
                  const SizedBox(height: 16),
                  Container(
                    height: 48,
                    decoration: BoxDecoration(
                      gradient: PartnerTheme.saffronGradient,
                      borderRadius: BorderRadius.circular(14),
                      boxShadow:
                          PartnerTheme.glow(PartnerTheme.saffron, blur: 8),
                    ),
                    child: Material(
                      color: Colors.transparent,
                      child: InkWell(
                        borderRadius: BorderRadius.circular(14),
                        onTap: _saving ? null : _save,
                        child: Center(
                          child: _saving
                              ? const SizedBox(
                                  width: 20,
                                  height: 20,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                    valueColor: AlwaysStoppedAnimation<Color>(
                                        Colors.white),
                                  ),
                                )
                              : const Text(
                                  'Save Profile Updates',
                                  style: TextStyle(
                                    fontSize: 13.5,
                                    fontWeight: FontWeight.w800,
                                    color: Colors.white,
                                  ),
                                ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 18),

          // Appearance Theme Switcher
          PartnerCard(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    const Icon(Icons.palette_outlined,
                        size: 20, color: PartnerTheme.saffron),
                    const SizedBox(width: 8),
                    const Text(
                      'App Theme Mode',
                      style:
                          TextStyle(fontSize: 14, fontWeight: FontWeight.w800),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                SegmentedButton<ThemeMode>(
                  segments: const [
                    ButtonSegment(
                      value: ThemeMode.light,
                      icon: Icon(Icons.light_mode_rounded),
                      label: Text('Light'),
                    ),
                    ButtonSegment(
                      value: ThemeMode.dark,
                      icon: Icon(Icons.dark_mode_rounded),
                      label: Text('Dark'),
                    ),
                    ButtonSegment(
                      value: ThemeMode.system,
                      icon: Icon(Icons.brightness_auto_rounded),
                      label: Text('System'),
                    ),
                  ],
                  selected: {session.themeMode},
                  onSelectionChanged: (set) {
                    if (set.isNotEmpty) session.setThemeMode(set.first);
                  },
                ),
              ],
            ),
          ),
          const SizedBox(height: 18),

          // Partner Support Helpline
          PartnerCard(
            padding: const EdgeInsets.all(16),
            borderColor: PartnerTheme.gold.withValues(alpha: 0.3),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: PartnerTheme.gold.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(Icons.headset_mic_rounded,
                      color: PartnerTheme.gold, size: 22),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Acharya Support Desk',
                        style: TextStyle(
                            fontSize: 14, fontWeight: FontWeight.w800),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        'Dedicated 24x7 helpline for consultation queries',
                        style: TextStyle(
                          fontSize: 11.5,
                          color: dark ? Colors.white60 : Colors.black54,
                        ),
                      ),
                    ],
                  ),
                ),
                TextButton(
                  onPressed: () => showSnack(
                      context, 'Astrologer Support helpline: +91 99990 00000'),
                  child: const Text('Contact'),
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),

          // Logout Button
          OutlinedButton.icon(
            onPressed: () => _logout(context),
            icon:
                const Icon(Icons.logout_rounded, color: PartnerTheme.crimson),
            label: const Text(
              'Log Out of Portal',
              style: TextStyle(
                color: PartnerTheme.crimson,
                fontWeight: FontWeight.w800,
              ),
            ),
            style: OutlinedButton.styleFrom(
              minimumSize: const Size.fromHeight(50),
              side: BorderSide(
                  color: PartnerTheme.crimson.withValues(alpha: 0.4)),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(14),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _credItem(String value, String label) {
    return Column(
      children: [
        Text(
          value,
          style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w900),
        ),
        const SizedBox(height: 2),
        Text(
          label,
          style: TextStyle(
            fontSize: 10.5,
            fontWeight: FontWeight.w600,
            color: Theme.of(context).brightness == Brightness.dark
                ? Colors.white60
                : Colors.black54,
          ),
        ),
      ],
    );
  }

  Widget _rateCard(
      String title, String rate, IconData icon, Color color, bool dark) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 8),
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.1),
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: color.withValues(alpha: 0.3)),
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 20),
            const SizedBox(height: 6),
            Text(
              rate,
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w900,
                color: color,
              ),
            ),
            const SizedBox(height: 2),
            Text(
              title,
              style: TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.w600,
                color: dark ? Colors.white60 : Colors.black54,
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _logout(BuildContext context) async {
    await context.read<PartnerSession>().logout();
    if (context.mounted) {
      Navigator.of(context)
          .pushNamedAndRemoveUntil(LoginScreen.route, (r) => false);
    }
  }
}
