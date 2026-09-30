import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../../theme/customer_theme.dart';
import '../../widgets/customer_widgets.dart';
import '../main_shell.dart';

/// 6-digit OTP verification screen with sacred styling
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

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    final args =
        ModalRoute.of(context)?.settings.arguments as Map<String, dynamic>?;
    _contactNo ??= args?['contactNo'] as String?;
    _countryCode ??= args?['countryCode'] as String? ?? '+91';
    final testOtp = args?['testOtp'] as String?;
    if (testOtp != null && testOtp.isNotEmpty && _otp.text.isEmpty) {
      _otp.text = testOtp;
    }
  }

  @override
  void dispose() {
    _otp.dispose();
    super.dispose();
  }

  Future<void> _verify() async {
    if (_otp.text.trim().length < 4) {
      showSnack(context, 'Enter the OTP you received', error: true);
      return;
    }
    setState(() => _verifying = true);
    try {
      await context
          .read<AppSession>()
          .login(contactNo: _contactNo!, countryCode: _countryCode!);
      if (!mounted) return;
      await context.read<AppSession>().refreshUser();
      if (!mounted) return;
      Navigator.of(context)
          .pushNamedAndRemoveUntil(MainShell.route, (r) => false);
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    } catch (e) {
      if (mounted) {
        showSnack(
          context,
          'Verification failed. Please try again.',
          error: true,
        );
      }
    } finally {
      if (mounted) setState(() => _verifying = false);
    }
  }

  Future<void> _resend() async {
    try {
      await context.read<AppSession>().sendOtp(
            contactNo: _contactNo!,
            countryCode: _countryCode!,
          );
      if (mounted) showSnack(context, 'OTP resent successfully');
    } catch (_) {
      if (mounted) showSnack(context, 'Could not resend OTP', error: true);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Verify Number'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 20),
          onPressed: () => Navigator.of(context).pop(),
        ),
      ),
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
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Center(
                  child: Container(
                    padding: const EdgeInsets.all(18),
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      gradient: CustomerTheme.goldGradient,
                      boxShadow: [
                        BoxShadow(
                          color: CustomerTheme.brandGold
                              .withValues(alpha: isDark ? 0.3 : 0.4),
                          blurRadius: 20,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: const Icon(
                      Icons.mark_email_read_rounded,
                      size: 38,
                      color: Colors.white,
                    ),
                  ),
                ),
                const SizedBox(height: 20),
                Text(
                  'Enter 6-Digit Code',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.w900,
                    color: isDark ? Colors.white : const Color(0xFF1E1A16),
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  'A verification OTP was sent to $_countryCode ${_contactNo ?? ''}',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 13.5,
                    color: isDark ? Colors.white60 : Colors.black54,
                  ),
                ),
                const SizedBox(height: 28),
                SacredCard(
                  padding: const EdgeInsets.all(22),
                  elevation: 2,
                  child: Column(
                    children: [
                      TextField(
                        controller: _otp,
                        keyboardType: TextInputType.number,
                        maxLength: 6,
                        textAlign: TextAlign.center,
                        style: const TextStyle(
                          fontSize: 28,
                          fontWeight: FontWeight.w900,
                          letterSpacing: 14,
                        ),
                        decoration: InputDecoration(
                          hintText: '••••••',
                          counterText: '',
                          contentPadding:
                              const EdgeInsets.symmetric(vertical: 16),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: const BorderSide(
                              color: CustomerTheme.brandGold,
                              width: 1.5,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 22),
                      SacredButton(
                        text: 'Verify & Enter Sanctum',
                        icon: Icons.check_circle_outline_rounded,
                        isLoading: _verifying,
                        onPressed: _verifying ? null : _verify,
                      ),
                      const SizedBox(height: 16),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            "Didn't receive the OTP? ",
                            style: TextStyle(
                              fontSize: 13,
                              color: isDark ? Colors.white60 : Colors.black54,
                            ),
                          ),
                          GestureDetector(
                            onTap: _resend,
                            child: Text(
                              'Resend Code',
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w700,
                                color: CustomerTheme.brandSaffron,
                                decoration: TextDecoration.underline,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
