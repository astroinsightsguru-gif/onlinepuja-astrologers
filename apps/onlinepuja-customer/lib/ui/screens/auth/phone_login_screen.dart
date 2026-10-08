import 'package:flutter/material.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../../theme/customer_theme.dart';
import '../../widgets/customer_widgets.dart';
import '../main_shell.dart';
import 'otp_screen.dart';

/// Divine devotee authentication screen with mobile OTP & Google sign-in
class PhoneLoginScreen extends StatefulWidget {
  const PhoneLoginScreen({super.key});

  static const route = '/login';

  @override
  State<PhoneLoginScreen> createState() => _PhoneLoginScreenState();
}

class _PhoneLoginScreenState extends State<PhoneLoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _phone = TextEditingController();
  String _countryCode = '+91';
  bool _sending = false;

  static const _codes = ['+91', '+1', '+44', '+61', '+65', '+971', '+977'];

  @override
  void dispose() {
    _phone.dispose();
    super.dispose();
  }

  Future<void> _continue() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _sending = true);
    final session = context.read<AppSession>();
    try {
      final otp = await session.sendOtp(
        contactNo: _phone.text.trim(),
        countryCode: _countryCode,
      );
      if (!mounted) return;
      Navigator.of(context).pushNamed(OtpScreen.route, arguments: {
        'contactNo': _phone.text.trim(),
        'countryCode': _countryCode,
        'testOtp': otp,
      });
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    } catch (e) {
      if (mounted) {
        showSnack(
          context,
          'Could not send OTP. Please try again.',
          error: true,
        );
      }
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  Future<void> _continueWithGoogle() async {
    setState(() => _sending = true);
    try {
      const clientId =
          '943854607420-9q5ro9tfc1ms9n3987ovb8vsidlcs04r.apps.googleusercontent.com';
      final googleSignIn = GoogleSignIn(
        clientId: clientId,
        serverClientId: clientId,
        scopes: ['email', 'profile'],
      );
      final account = await googleSignIn.signIn();
      if (account == null) {
        if (mounted) setState(() => _sending = false);
        return;
      }
      if (!mounted) return;
      final session = context.read<AppSession>();
      await session.login(
        contactNo: '',
        countryCode: '+91',
        email: account.email,
        name: account.displayName ?? 'Devotee',
      );
      if (!mounted) return;
      await session.refreshUser();
      if (!mounted) return;
      Navigator.of(context)
          .pushNamedAndRemoveUntil(MainShell.route, (r) => false);
    } catch (e) {
      if (mounted) {
        showSnack(context, 'Google Sign-In: $e', error: true);
      }
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  Future<void> _skipLogin() async {
    setState(() => _sending = true);
    try {
      final session = context.read<AppSession>();
      await session.continueAsGuest();
      if (!mounted) return;
      Navigator.of(context)
          .pushNamedAndRemoveUntil(MainShell.route, (r) => false);
    } catch (e) {
      if (mounted) {
        showSnack(context, 'Guest login: $e', error: true);
      }
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      body: Container(
        width: double.infinity,
        height: double.infinity,
        decoration: BoxDecoration(
          gradient: isDark
              ? CustomerTheme.cosmicDarkGradient
              : const LinearGradient(
                  colors: [
                    Color(0xFFFFFBEB),
                    Color(0xFFFEF3C7),
                    Color(0xFFFAF7F2),
                  ],
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                ),
        ),
        child: SafeArea(
          child: Column(
            children: [
              // Top-right Skip Button
              Padding(
                padding: const EdgeInsets.only(top: 8, right: 16),
                child: Align(
                  alignment: Alignment.topRight,
                  child: TextButton.icon(
                    onPressed: _sending ? null : _skipLogin,
                    icon: const Icon(Icons.arrow_forward_rounded, size: 16),
                    label: const Text(
                      'Skip',
                      style: TextStyle(
                        fontWeight: FontWeight.w800,
                        fontSize: 14,
                        letterSpacing: 0.2,
                      ),
                    ),
                    style: TextButton.styleFrom(
                      foregroundColor:
                          isDark ? Colors.white : CustomerTheme.brandKumkum,
                      backgroundColor: isDark
                          ? Colors.white.withValues(alpha: 0.12)
                          : CustomerTheme.brandGold.withValues(alpha: 0.2),
                      padding: const EdgeInsets.symmetric(
                          horizontal: 16, vertical: 8),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(20),
                        side: BorderSide(
                          color: CustomerTheme.brandGold.withValues(alpha: 0.4),
                          width: 1,
                        ),
                      ),
                    ),
                  ),
                ),
              ),
              Expanded(
                child: Center(
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 24, vertical: 12),
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          // Sacred Sun Emblem
                          Center(
                            child: Container(
                              padding: const EdgeInsets.all(18),
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                gradient: CustomerTheme.goldGradient,
                                boxShadow: [
                            BoxShadow(
                              color: CustomerTheme.brandGold
                                  .withValues(alpha: isDark ? 0.35 : 0.45),
                              blurRadius: 24,
                              offset: const Offset(0, 6),
                            ),
                          ],
                        ),
                        child: const Icon(
                          Icons.wb_sunny_rounded,
                          size: 40,
                          color: Colors.white,
                        ),
                      ),
                    ),
                    const SizedBox(height: 18),

                    // Title
                    Text(
                      'Welcome to Online Puja',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.w900,
                        letterSpacing: -0.3,
                        color: isDark ? Colors.white : const Color(0xFF1E1A16),
                      ),
                    ),
                    const SizedBox(height: 6),
                    Text(
                      'Enter your mobile number to consult certified Vedic pandits & astrologers',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 13.5,
                        color: isDark ? Colors.white60 : Colors.black54,
                        height: 1.35,
                      ),
                    ),
                    const SizedBox(height: 32),

                    // Phone Input Card
                    SacredCard(
                      padding: const EdgeInsets.all(18),
                      elevation: 2,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'PHONE NUMBER',
                            style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w800,
                              letterSpacing: 0.8,
                              color: CustomerTheme.brandSaffron,
                            ),
                          ),
                          const SizedBox(height: 10),
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 10,
                                  vertical: 4,
                                ),
                                decoration: BoxDecoration(
                                  color: isDark
                                      ? CustomerTheme.cosmicCardElevated
                                      : const Color(0xFFF9FAFB),
                                  borderRadius: BorderRadius.circular(12),
                                  border: Border.all(
                                    color: isDark
                                        ? CustomerTheme.cosmicBorderDark
                                        : CustomerTheme.lightBorder,
                                  ),
                                ),
                                child: DropdownButtonHideUnderline(
                                  child: DropdownButton<String>(
                                    value: _countryCode,
                                    dropdownColor: isDark
                                        ? CustomerTheme.cosmicCardDark
                                        : Colors.white,
                                    items: _codes
                                        .map((c) => DropdownMenuItem(
                                              value: c,
                                              child: Text(
                                                c,
                                                style: TextStyle(
                                                  fontWeight: FontWeight.w700,
                                                  color: isDark
                                                      ? Colors.white
                                                      : Colors.black87,
                                                ),
                                              ),
                                            ))
                                        .toList(),
                                    onChanged: (v) => setState(
                                      () => _countryCode = v ?? '+91',
                                    ),
                                  ),
                                ),
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: TextFormField(
                                  controller: _phone,
                                  keyboardType: TextInputType.phone,
                                  maxLength: 12,
                                  style: TextStyle(
                                    fontWeight: FontWeight.w600,
                                    color: isDark
                                        ? Colors.white
                                        : Colors.black87,
                                  ),
                                  decoration: InputDecoration(
                                    hintText: 'Enter 10-digit number',
                                    counterText: '',
                                    prefixIcon: Icon(
                                      Icons.phone_iphone_rounded,
                                      color: CustomerTheme.brandSaffron,
                                      size: 20,
                                    ),
                                  ),
                                  validator: (v) =>
                                      (v == null || v.trim().length < 7)
                                          ? 'Enter a valid mobile number'
                                          : null,
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 18),
                          SacredButton(
                            text: 'Send Verification OTP',
                            icon: Icons.lock_outline_rounded,
                            isLoading: _sending,
                            onPressed: _sending ? null : _continue,
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 24),

                    // Divider
                    Row(
                      children: [
                        Expanded(
                          child: Divider(
                            color: isDark
                                ? Colors.white12
                                : Colors.black.withValues(alpha: 0.1),
                          ),
                        ),
                        Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 14),
                          child: Text(
                            'OR CONNECT WITH',
                            style: TextStyle(
                              color: isDark ? Colors.white38 : Colors.black38,
                              fontSize: 11,
                              fontWeight: FontWeight.w700,
                              letterSpacing: 0.6,
                            ),
                          ),
                        ),
                        Expanded(
                          child: Divider(
                            color: isDark
                                ? Colors.white12
                                : Colors.black.withValues(alpha: 0.1),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 20),

                    // Google Sign-In Button
                    OutlinedButton(
                      style: OutlinedButton.styleFrom(
                        backgroundColor: isDark
                            ? CustomerTheme.cosmicCardDark
                            : Colors.white,
                        side: BorderSide(
                          color: isDark
                              ? CustomerTheme.brandGold.withValues(alpha: 0.3)
                              : CustomerTheme.lightBorder,
                        ),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14),
                        ),
                        padding: const EdgeInsets.symmetric(vertical: 14),
                      ),
                      onPressed: _sending ? null : _continueWithGoogle,
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(
                            Icons.g_mobiledata_rounded,
                            size: 28,
                            color: Color(0xFFEA4335),
                          ),
                          const SizedBox(width: 8),
                          Text(
                            'Continue with Google',
                            style: TextStyle(
                              fontSize: 14.5,
                              fontWeight: FontWeight.w700,
                              color: isDark ? Colors.white : Colors.black87,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 14),

                    // Skip & Explore as Guest Button
                    OutlinedButton.icon(
                      style: OutlinedButton.styleFrom(
                        side: BorderSide(
                          color: CustomerTheme.brandGold.withValues(alpha: 0.6),
                          width: 1.2,
                        ),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14),
                        ),
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        foregroundColor:
                            isDark ? Colors.white : CustomerTheme.brandKumkum,
                      ),
                      onPressed: _sending ? null : _skipLogin,
                      icon: const Icon(Icons.explore_outlined, size: 20),
                      label: const Text(
                        'Skip & Explore as Guest',
                        style: TextStyle(
                          fontSize: 14.5,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ),
                    const SizedBox(height: 24),

                    // Privacy Note
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.verified_user_rounded,
                          size: 14,
                          color: CustomerTheme.sacredEmerald,
                        ),
                        const SizedBox(width: 6),
                        Expanded(
                          child: Text(
                            '100% Confidential & Secure. By continuing, you agree to our Terms and Privacy Policy.',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                              fontSize: 11,
                              color: isDark ? Colors.white38 : Colors.black45,
                              height: 1.3,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ],
    ),
  ),
),
);
}
}
