import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../main_shell.dart';

/// 6-digit OTP verification. The backend sends the OTP during
/// `checkContactNoExistForUser`; login happens on verify.
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
  }

  Future<void> _verify() async {
    if (_otp.text.trim().length < 4) {
      showSnack(context, 'Enter the OTP you received', error: true);
      return;
    }
    setState(() => _verifying = true);
    try {
      // Legacy contract: OTP is validated server-side; loginAppUser
      // exchanges the verified contact for a JWT session.
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
        showSnack(context, 'Verification failed. Please try again.',
            error: true);
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
      if (mounted) showSnack(context, 'OTP resent');
    } catch (_) {
      if (mounted) showSnack(context, 'Could not resend OTP', error: true);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(),
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.symmetric(horizontal: 28),
          children: [
            const SizedBox(height: 18),
            Text('Verify OTP',
                style: Theme.of(context).textTheme.headlineSmall),
            const SizedBox(height: 8),
            Text(
              'Enter the 6-digit code sent to $_countryCode ${_contactNo ?? ''}',
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                  color: Theme.of(context).colorScheme.outline),
            ),
            const SizedBox(height: 26),
            TextField(
              controller: _otp,
              keyboardType: TextInputType.number,
              maxLength: 6,
              textAlign: TextAlign.center,
              style: const TextStyle(
                  fontSize: 26, letterSpacing: 12, fontWeight: FontWeight.w700),
              decoration: const InputDecoration(
                hintText: '••••••',
                counterText: '',
              ),
            ),
            const SizedBox(height: 22),
            FilledButton(
              style: FilledButton.styleFrom(
                  minimumSize: const Size.fromHeight(52)),
              onPressed: _verifying ? null : _verify,
              child: _verifying
                  ? const SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(strokeWidth: 2),
                    )
                  : const Text('Verify & Continue'),
            ),
            TextButton(
              onPressed: _verifying ? null : _resend,
              child: const Text('Resend OTP'),
            ),
          ],
        ),
      ),
    );
  }
}
