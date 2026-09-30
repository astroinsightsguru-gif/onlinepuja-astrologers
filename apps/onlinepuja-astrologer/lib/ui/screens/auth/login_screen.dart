import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../../theme/partner_theme.dart';
import '../../widgets/partner_widgets.dart';
import 'otp_screen.dart';
import 'partner_register_screen.dart';

/// Mobile number login for astrologers (+91 default, international supported).
class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  static const route = '/login';

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
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
    final session = context.read<PartnerSession>();
    try {
      await session.sendOtp(
        contactNo: _phone.text.trim(),
        countryCode: _countryCode,
      );
      if (!mounted) return;
      Navigator.of(context).pushNamed(OtpScreen.route, arguments: {
        'contactNo': _phone.text.trim(),
        'countryCode': _countryCode,
      });
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    } catch (_) {
      if (mounted) {
        showSnack(context, 'Could not send OTP. Please try again.',
            error: true);
      }
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const SizedBox(height: 12),

                  // Spiritual Crest Icon
                  Center(
                    child: Container(
                      width: 80,
                      height: 80,
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        gradient: PartnerTheme.luxuryGold,
                        boxShadow: PartnerTheme.glow(
                          PartnerTheme.saffron,
                          blur: 20,
                          spread: 2,
                        ),
                      ),
                      child: Container(
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: dark ? PartnerTheme.darkBg : Colors.white,
                        ),
                        child: const Icon(
                          Icons.auto_awesome,
                          size: 34,
                          color: PartnerTheme.saffron,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 18),

                  // Portal Title
                  Text(
                    'Astrologer Partner Portal',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.w900,
                      color: dark ? Colors.white : const Color(0xFF221B14),
                      letterSpacing: -0.3,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    'Sign in with your verified mobile number to accept devotee consultations.',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w500,
                      color: dark ? Colors.white60 : const Color(0xFF6B7280),
                      height: 1.4,
                    ),
                  ),
                  const SizedBox(height: 28),

                  // Phone Input Container
                  PartnerCard(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'REGISTERED MOBILE NUMBER',
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w800,
                            letterSpacing: 0.8,
                            color: PartnerTheme.saffron,
                          ),
                        ),
                        const SizedBox(height: 10),
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              height: 54,
                              padding: const EdgeInsets.symmetric(horizontal: 10),
                              decoration: BoxDecoration(
                                color: dark
                                    ? PartnerTheme.darkSurface
                                    : const Color(0xFFF7F5F0),
                                borderRadius: BorderRadius.circular(14),
                                border: Border.all(
                                  color: dark
                                      ? PartnerTheme.darkBorder
                                      : const Color(0xFFE2D9CC),
                                ),
                              ),
                              child: DropdownButtonHideUnderline(
                                child: DropdownButton<String>(
                                  value: _countryCode,
                                  icon: const Icon(Icons.keyboard_arrow_down_rounded, size: 20),
                                  items: _codes
                                      .map((c) => DropdownMenuItem(
                                            value: c,
                                            child: Text(
                                              c,
                                              style: const TextStyle(
                                                fontWeight: FontWeight.w700,
                                                fontSize: 14,
                                              ),
                                            ),
                                          ))
                                      .toList(),
                                  onChanged: (v) =>
                                      setState(() => _countryCode = v ?? '+91'),
                                ),
                              ),
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              child: TextFormField(
                                controller: _phone,
                                keyboardType: TextInputType.phone,
                                maxLength: 12,
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.w700,
                                  letterSpacing: 0.5,
                                ),
                                decoration: InputDecoration(
                                  hintText: 'Enter 10-digit number',
                                  counterText: '',
                                  prefixIcon: const Icon(Icons.phone_iphone_rounded),
                                  filled: true,
                                  fillColor: dark
                                      ? PartnerTheme.darkSurface
                                      : const Color(0xFFF7F5F0),
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 14, vertical: 16),
                                  border: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(14),
                                    borderSide: BorderSide(
                                      color: dark
                                          ? PartnerTheme.darkBorder
                                          : const Color(0xFFE2D9CC),
                                    ),
                                  ),
                                  enabledBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(14),
                                    borderSide: BorderSide(
                                      color: dark
                                          ? PartnerTheme.darkBorder
                                          : const Color(0xFFE2D9CC),
                                    ),
                                  ),
                                  focusedBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(14),
                                    borderSide: const BorderSide(
                                      color: PartnerTheme.saffron,
                                      width: 1.8,
                                    ),
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
                      ],
                    ),
                  ),
                  const SizedBox(height: 22),

                  // Send OTP Button
                  Container(
                    height: 52,
                    decoration: BoxDecoration(
                      gradient: PartnerTheme.saffronGradient,
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: PartnerTheme.glow(PartnerTheme.saffron, blur: 14),
                    ),
                    child: Material(
                      color: Colors.transparent,
                      child: InkWell(
                        borderRadius: BorderRadius.circular(16),
                        onTap: _sending ? null : _continue,
                        child: Center(
                          child: _sending
                              ? const SizedBox(
                                  width: 22,
                                  height: 22,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2.2,
                                    valueColor: AlwaysStoppedAnimation<Color>(
                                        Colors.white),
                                  ),
                                )
                              : const Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Icon(Icons.send_rounded,
                                        size: 18, color: Colors.white),
                                    SizedBox(width: 8),
                                    Text(
                                      'GET OTP VERIFICATION',
                                      style: TextStyle(
                                        fontSize: 14,
                                        fontWeight: FontWeight.w800,
                                        letterSpacing: 0.8,
                                        color: Colors.white,
                                      ),
                                    ),
                                  ],
                                ),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 26),

                  // Astrologer Highlights Strip
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 16, vertical: 12),
                    decoration: BoxDecoration(
                      color: dark
                          ? PartnerTheme.darkSurface
                          : const Color(0xFFFAF6EE),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(
                        color: dark
                            ? PartnerTheme.darkBorder
                            : const Color(0xFFECE4D6),
                      ),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceAround,
                      children: [
                        _featurePill(Icons.shield_outlined, '100% Verified'),
                        _featurePill(Icons.currency_rupee_rounded, 'Daily Payouts'),
                        _featurePill(Icons.headset_mic_outlined, '24x7 Support'),
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),

                  // Register Link
                  Center(
                    child: TextButton.icon(
                      onPressed: () => Navigator.of(context).push(
                        MaterialPageRoute(
                            builder: (_) => const PartnerRegisterScreen()),
                      ),
                      icon: const Icon(Icons.person_add_alt_1_rounded, size: 18),
                      label: const Text(
                        'New Astrologer? Register Here',
                        style: TextStyle(
                          fontWeight: FontWeight.w700,
                          fontSize: 13.5,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _featurePill(IconData icon, String text) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 15, color: PartnerTheme.gold),
        const SizedBox(width: 5),
        Text(
          text,
          style: const TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }
}
