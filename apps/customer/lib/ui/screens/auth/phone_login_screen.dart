import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import 'otp_screen.dart';

/// Mobile number login (+91 default, international codes supported).
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
    } catch (e) {
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
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 28),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Align(child: Brand.logo(showName: true)),
                  const SizedBox(height: 34),
                  Text(
                    'Sign in to continue',
                    textAlign: TextAlign.center,
                    style: Theme.of(context).textTheme.titleLarge,
                  ),
                  const SizedBox(height: 26),
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      DropdownButton<String>(
                        value: _countryCode,
                        underline: const SizedBox.shrink(),
                        items: _codes
                            .map((c) => DropdownMenuItem(
                                  value: c,
                                  child: Text(c),
                                ))
                            .toList(),
                        onChanged: (v) =>
                            setState(() => _countryCode = v ?? '+91'),
                      ),
                      const SizedBox(width: 6),
                      Expanded(
                        child: TextFormField(
                          controller: _phone,
                          keyboardType: TextInputType.phone,
                          maxLength: 12,
                          decoration: const InputDecoration(
                            hintText: 'Mobile number',
                            counterText: '',
                            prefixIcon: Icon(Icons.phone_rounded),
                          ),
                          validator: (v) =>
                              (v == null || v.trim().length < 7)
                                  ? 'Enter a valid mobile number'
                                  : null,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 22),
                  FilledButton(
                    style: FilledButton.styleFrom(
                        minimumSize: const Size.fromHeight(52)),
                    onPressed: _sending ? null : _continue,
                    child: _sending
                        ? const SizedBox(
                            width: 20,
                            height: 20,
                            child:
                                CircularProgressIndicator(strokeWidth: 2),
                          )
                        : const Text('Send OTP'),
                  ),
                  const SizedBox(height: 14),
                  Text(
                    'By continuing you agree to our Terms of Use and Privacy Policy.',
                    textAlign: TextAlign.center,
                    style: Theme.of(context)
                        .textTheme
                        .bodySmall
                        ?.copyWith(color: scheme.outline),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
