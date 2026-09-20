import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

/// Partner sign-up wizard (legacy 6-step flow, condensed to 2 steps for the
/// fields the current `partner/register` API requires):
/// step 1 = personal (name, email, phone) · step 2 = business + password.
class PartnerRegisterScreen extends StatefulWidget {
  const PartnerRegisterScreen({super.key});

  @override
  State<PartnerRegisterScreen> createState() => _PartnerRegisterScreenState();
}

class _PartnerRegisterScreenState extends State<PartnerRegisterScreen> {
  final _formKeys = [GlobalKey<FormState>(), GlobalKey<FormState>()];
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _phone = TextEditingController();
  final _business = TextEditingController();
  final _address = TextEditingController();
  final _password = TextEditingController();
  final _confirm = TextEditingController();
  int _step = 0;
  bool _saving = false;
  bool _done = false;

  @override
  void dispose() {
    for (final c in [_name, _email, _phone, _business, _address, _password, _confirm]) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKeys[1].currentState!.validate()) return;
    setState(() => _saving = true);
    try {
      await PartnerApi.instance.register(
        name: _name.text.trim(),
        email: _email.text.trim(),
        phone: _phone.text.trim(),
        password: _password.text,
        businessName: _business.text.trim(),
        businessAddress: _address.text.trim(),
      );
      if (mounted) setState(() => _done = true);
    } on ApiException catch (e) {
      if (mounted) {
        showSnack(
          context,
          e.errors == null || e.errors!.isEmpty
              ? e.message
              : e.errors!.values
                  .whereType<List>()
                  .map((l) => l.join('\n'))
                  .join('\n'),
          error: true,
        );
      }
    } catch (e) {
      if (mounted) showSnack(context, e.toString(), error: true);
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_done) return _successView(context);
    return Scaffold(
      appBar: AppBar(
          title:
              Text(_step == 0 ? 'Register · 1 of 2' : 'Register · 2 of 2')),
      body: Stepper(
        currentStep: _step,
        onStepTapped: (i) => setState(() => _step = i),
        controlsBuilder: (_, details) => Padding(
          padding: const EdgeInsets.only(top: 14),
          child: Row(children: [
            FilledButton(
              onPressed: _saving
                  ? null
                  : () {
                      if (_step == 0) {
                        if (_formKeys[0].currentState!.validate()) {
                          setState(() => _step = 1);
                        }
                      } else {
                        _submit();
                      }
                    },
              child: _saving
                  ? const SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(strokeWidth: 2))
                  : Text(_step == 0 ? 'Continue' : 'Create account'),
            ),
            if (_step > 0)
              TextButton(
                onPressed: () => setState(() => _step = 0),
                child: const Text('Back'),
              ),
          ]),
        ),
        steps: [
          Step(
            title: const Text('Personal details'),
            isActive: _step >= 0,
            state: _step > 0 ? StepState.complete : StepState.indexed,
            content: Form(
              key: _formKeys[0],
              child: Column(children: [
                TextFormField(
                  controller: _name,
                  decoration: const InputDecoration(labelText: 'Full name'),
                  validator: (v) => (v == null || v.trim().isEmpty)
                      ? 'Name is required'
                      : null,
                ),
                TextFormField(
                  controller: _email,
                  decoration: const InputDecoration(labelText: 'Email'),
                  keyboardType: TextInputType.emailAddress,
                  validator: (v) => (v == null || !v.contains('@'))
                      ? 'Valid email required'
                      : null,
                ),
                TextFormField(
                  controller: _phone,
                  decoration:
                      const InputDecoration(labelText: 'Phone (+91…)'),
                  keyboardType: TextInputType.phone,
                  validator: (v) => (v == null || v.trim().length < 8)
                      ? 'Valid phone required'
                      : null,
                ),
              ]),
            ),
          ),
          Step(
            title: const Text('Business & password'),
            isActive: _step >= 1,
            content: Form(
              key: _formKeys[1],
              child: Column(children: [
                TextFormField(
                  controller: _business,
                  decoration: const InputDecoration(
                      labelText: 'Business / brand name'),
                  validator: (v) =>
                      (v == null || v.trim().isEmpty) ? 'Required' : null,
                ),
                TextFormField(
                  controller: _address,
                  decoration:
                      const InputDecoration(labelText: 'Business address'),
                  maxLines: 2,
                  validator: (v) =>
                      (v == null || v.trim().isEmpty) ? 'Required' : null,
                ),
                TextFormField(
                  controller: _password,
                  decoration: const InputDecoration(labelText: 'Password'),
                  obscureText: true,
                  validator: (v) => (v == null || v.length < 8)
                      ? 'Minimum 8 characters'
                      : null,
                ),
                TextFormField(
                  controller: _confirm,
                  decoration:
                      const InputDecoration(labelText: 'Confirm password'),
                  obscureText: true,
                  validator: (v) =>
                      (v != _password.text) ? 'Passwords do not match' : null,
                ),
              ]),
            ),
          ),
        ],
      ),
    );
  }

  Widget _successView(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.verified_rounded, size: 72, color: scheme.primary),
              const SizedBox(height: 18),
              Text('Registration submitted 🎉',
                  style: Theme.of(context)
                      .textTheme
                      .headlineSmall
                      ?.copyWith(fontWeight: FontWeight.w800),
                  textAlign: TextAlign.center),
              const SizedBox(height: 10),
              Text(
                'Our team will verify your details and approve your account. '
                'You will be able to log in once approved.',
                textAlign: TextAlign.center,
                style: Theme.of(context)
                    .textTheme
                    .bodyMedium
                    ?.copyWith(color: scheme.outline),
              ),
              const SizedBox(height: 26),
              FilledButton(
                onPressed: () => Navigator.of(context).pop(),
                child: const Text('Back to login'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
