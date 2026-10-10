import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../state/app_session.dart';
import '../theme/partner_theme.dart';
import 'auth/login_screen.dart';
import 'home/home_shell.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  static const route = '/';

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _anim;
  late Animation<double> _scale;
  late Animation<double> _fade;

  @override
  void initState() {
    super.initState();
    _anim = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1400),
    );
    _scale = Tween<double>(begin: 0.85, end: 1.0).animate(
      CurvedAnimation(parent: _anim, curve: Curves.easeOutBack),
    );
    _fade = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _anim, curve: Curves.easeIn),
    );
    _anim.forward();
    _bootstrap();
  }

  @override
  void dispose() {
    _anim.dispose();
    super.dispose();
  }

  Future<void> _bootstrap() async {
    await Future<void>.delayed(const Duration(milliseconds: 1800));
    if (!mounted) return;
    final session = context.read<PartnerSession>();
    if (!mounted) return;
    Navigator.of(context).pushNamedAndRemoveUntil(
      session.isLoggedIn ? HomeShell.route : LoginScreen.route,
      (r) => false,
    );
  }

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      body: Stack(
        children: [
          // Background Cosmic Gradient
          Container(
            decoration: BoxDecoration(
              gradient: dark
                  ? PartnerTheme.cosmicDarkGradient
                  : const LinearGradient(
                      colors: [Color(0xFFFFF9F0), Color(0xFFFDECD2)],
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                    ),
            ),
          ),

          // Central Astrological Brand Crest
          Center(
            child: FadeTransition(
              opacity: _fade,
              child: ScaleTransition(
                scale: _scale,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    // Glowing Crest Container with Official Astrologer Logo
                    Container(
                      width: 120,
                      height: 120,
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: dark ? PartnerTheme.darkBg : Colors.white,
                        boxShadow: PartnerTheme.glow(
                          PartnerTheme.saffron,
                          blur: 28,
                          spread: 4,
                        ),
                      ),
                      child: ClipOval(
                        child: Image.asset(
                          'assets/images/app_logo.png',
                          fit: BoxFit.contain,
                          errorBuilder: (context, error, stackTrace) => const Icon(
                            Icons.auto_awesome,
                            size: 46,
                            color: PartnerTheme.saffron,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 24),

                    // App Title
                    ShaderMask(
                      shaderCallback: (bounds) =>
                          PartnerTheme.luxuryGold.createShader(bounds),
                      child: const Text(
                        'OnlinePuja.astro',
                        style: TextStyle(
                          fontSize: 26,
                          fontWeight: FontWeight.w900,
                          letterSpacing: 1.5,
                          color: Colors.white,
                        ),
                      ),
                    ),
                    const SizedBox(height: 6),

                    // Partner Pill
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 4),
                      decoration: BoxDecoration(
                        color: PartnerTheme.saffron.withValues(alpha: 0.15),
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(
                          color: PartnerTheme.saffron.withValues(alpha: 0.4),
                        ),
                      ),
                      child: const Text(
                        'ASTROLOGER PORTAL',
                        style: TextStyle(
                          fontSize: 11.5,
                          fontWeight: FontWeight.w800,
                          letterSpacing: 1.5,
                          color: PartnerTheme.saffron,
                        ),
                      ),
                    ),
                    const SizedBox(height: 14),

                    Text(
                      'Vedic Wisdom · Devotee Consultations',
                      style: TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w500,
                        color: dark ? Colors.white60 : const Color(0xFF6B7280),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),

          // Bottom Loading Spinner
          Positioned(
            bottom: 48,
            left: 0,
            right: 0,
            child: Center(
              child: Column(
                children: [
                  SizedBox(
                    width: 22,
                    height: 22,
                    child: CircularProgressIndicator(
                      strokeWidth: 2.2,
                      valueColor:
                          AlwaysStoppedAnimation<Color>(PartnerTheme.saffron),
                    ),
                  ),
                  const SizedBox(height: 10),
                  Text(
                    'Connecting to Portal...',
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                      color: dark ? Colors.white38 : Colors.black38,
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
}
