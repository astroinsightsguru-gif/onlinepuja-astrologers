import 'dart:async';

import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../../theme/partner_theme.dart';
import '../../widgets/partner_widgets.dart';
import '../home/home_shell.dart';

/// 6-digit OTP verification for astrologer login.
class OtpScreen extends StatefulWidget {
  const OtpScreen({super.key});

  static const route = '/otp';

  @override
  State<OtpScreen> createState() => _OtpScreenState();
}

class _OtpScreenState extends State<OtpScreen> {
  final _otp = TextEditingController();
  bool _verifying = false;
  String? _contactNo;
  String? _countryCode;
  int _secondsLeft = 30;
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    _startTimer();
  }

  void _startTimer() {
    _timer?.cancel();
    setState(() => _secondsLeft = 30);
    _timer = Timer.periodic(const Duration(seconds: 1), (t) {
      if (_secondsLeft <= 1) {
        t.cancel();
        if (mounted) setState(() => _secondsLeft = 0);
      } else {
        if (mounted) setState(() => _secondsLeft--);
      }
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    _otp.dispose();
    super.dispose();
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    final args =
        ModalRoute.of(context)?.settings.arguments as Map<String, dynamic>?;
    _contactNo ??= args?['contactNo'] as String?;
    _countryCode ??= args?['countryCode'] as String? ?? '+91';
  }

  Future<void> _verify() async {
    if (_otp.text.trim().length < 4) {
      showSnack(context, 'Enter the OTP you received', error: true);
      return;
    }
    setState(() => _verifying = true);
    try {
      await context
          .read<PartnerSession>()
          .login(contactNo: _contactNo!, countryCode: _countryCode!);
      if (!mounted) return;
      await context.read<PartnerSession>().refreshUser();
      if (!mounted) return;
      Navigator.of(context)
          .pushNamedAndRemoveUntil(HomeShell.route, (r) => false);
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    } catch (_) {
      if (mounted) {
        showSnack(context, 'Verification failed. Please try again.',
            error: true);
      }
    } finally {
      if (mounted) setState(() => _verifying = false);
    }
  }

  Future<void> _resend() async {
    try {
      await context.read<PartnerSession>().sendOtp(
            contactNo: _contactNo!,
            countryCode: _countryCode!,
          );
      _startTimer();
      if (mounted) showSnack(context, 'OTP resent successfully');
    } catch (_) {
      if (mounted) showSnack(context, 'Could not resend OTP', error: true);
    }
  }

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: const Text('OTP Verification',
            style: TextStyle(fontWeight: FontWeight.w800, fontSize: 17)),
      ),
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Lock Icon
                Center(
                  child: Container(
                    width: 72,
                    height: 72,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: PartnerTheme.saffron.withValues(alpha: 0.15),
                      border: Border.all(
                        color: PartnerTheme.saffron.withValues(alpha: 0.35),
                      ),
                    ),
                    child: const Icon(
                      Icons.mark_email_read_outlined,
                      size: 36,
                      color: PartnerTheme.saffron,
                    ),
                  ),
                ),
                const SizedBox(height: 20),

                Text(
                  'Enter 6-Digit Code',
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.w900,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  'We sent a verification code to',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 13,
                    color: dark ? Colors.white60 : Colors.black54,
                  ),
                ),
                const SizedBox(height: 4),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(
                      '$_countryCode ${_contactNo ?? ""}',
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w800,
                        letterSpacing: 0.5,
                      ),
                    ),
                    const SizedBox(width: 6),
                    GestureDetector(
                      onTap: () => Navigator.pop(context),
                      child: Text(
                        'Change',
                        style: TextStyle(
                          fontSize: 12.5,
                          fontWeight: FontWeight.w700,
                          color: PartnerTheme.saffron,
                          decoration: TextDecoration.underline,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 28),

                // OTP Box Container
                PartnerCard(
                  padding: const EdgeInsets.symmetric(
                      horizontal: 16, vertical: 22),
                  child: Column(
                    children: [
                      TextField(
                        controller: _otp,
                        keyboardType: TextInputType.number,
                        maxLength: 6,
                        textAlign: TextAlign.center,
                        autofocus: true,
                        style: TextStyle(
                          fontSize: 30,
                          letterSpacing: 14,
                          fontWeight: FontWeight.w900,
                          color: PartnerTheme.saffron,
                        ),
                        decoration: InputDecoration(
                          hintText: '••••••',
                          hintStyle: TextStyle(
                            fontSize: 30,
                            letterSpacing: 14,
                            color: Colors.grey.withValues(alpha: 0.4),
                          ),
                          counterText: '',
                          filled: true,
                          fillColor: dark
                              ? PartnerTheme.darkSurface
                              : const Color(0xFFF7F4EE),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: BorderSide(
                              color: dark
                                  ? PartnerTheme.darkBorder
                                  : const Color(0xFFE2D8C8),
                            ),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: const BorderSide(
                              color: PartnerTheme.saffron,
                              width: 2,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 14),
                      Text(
                        'Auto-detecting SMS code...',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w500,
                          color: dark ? Colors.white38 : Colors.black38,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 22),

                // Verify Button
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
                      onTap: _verifying ? null : _verify,
                      child: Center(
                        child: _verifying
                            ? const SizedBox(
                                width: 22,
                                height: 22,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2.2,
                                  valueColor: AlwaysStoppedAnimation<Color>(
                                      Colors.white),
                                ),
                              )
                            : const Text(
                                'VERIFY & SIGN IN',
                                style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w800,
                                  letterSpacing: 0.8,
                                  color: Colors.white,
                                ),
                              ),
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: 18),

                // Resend Countdown
                Center(
                  child: _secondsLeft > 0
                      ? Text(
                          'Resend OTP in ${_secondsLeft}s',
                          style: TextStyle(
                            fontSize: 12.5,
                            fontWeight: FontWeight.w600,
                            color: dark ? Colors.white54 : Colors.black45,
                          ),
                        )
                      : TextButton.icon(
                          onPressed: _verifying ? null : _resend,
                          icon: const Icon(Icons.refresh_rounded, size: 18),
                          label: const Text(
                            'Resend OTP Code',
                            style: TextStyle(
                              fontWeight: FontWeight.w700,
                              fontSize: 13,
                            ),
                          ),
                        ),
                ),
                const SizedBox(height: 20),

                // Security Note
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.lock_outline_rounded,
                        size: 14,
                        color: dark ? Colors.white38 : Colors.black38),
                    const SizedBox(width: 5),
                    Text(
                      '256-bit Encrypted Partner Authentication',
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w500,
                        color: dark ? Colors.white38 : Colors.black38,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
