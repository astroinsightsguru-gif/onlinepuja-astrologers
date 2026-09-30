import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import '../../theme/partner_theme.dart';
import '../../widgets/partner_widgets.dart';

/// Partner sign-up wizard:
/// Step 1: Personal Details (Name, Email, Phone, Expertise)
/// Step 2: Business / Practice & Security Password
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

  final Set<String> _selectedSkills = {'Vedic Astrology', 'Kundli Milan'};
  static const _availableSkills = [
    'Vedic Astrology',
    'Kundli Milan',
    'Numerology',
    'Tarot Reading',
    'Vastu Shastra',
    'Prashna Kundli',
    'Palmistry',
    'Gemology',
  ];

  @override
  void dispose() {
    for (final c in [
      _name,
      _email,
      _phone,
      _business,
      _address,
      _password,
      _confirm
    ]) {
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
    final dark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Astrologer Registration',
            style: TextStyle(fontWeight: FontWeight.w800, fontSize: 17)),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Stepper Progress Indicators
              Row(
                children: [
                  _stepIndicator(1, 'Personal', _step >= 0, _step == 0),
                  Expanded(
                    child: Container(
                      height: 2,
                      color: _step >= 1
                          ? PartnerTheme.saffron
                          : (dark ? Colors.white24 : Colors.black12),
                    ),
                  ),
                  _stepIndicator(2, 'Practice', _step >= 1, _step == 1),
                ],
              ),
              const SizedBox(height: 24),

              if (_step == 0) _stepOneForm(dark) else _stepTwoForm(dark),

              const SizedBox(height: 24),

              // Action Buttons
              Row(
                children: [
                  if (_step > 0) ...[
                    Expanded(
                      flex: 1,
                      child: OutlinedButton(
                        onPressed: () => setState(() => _step = 0),
                        style: OutlinedButton.styleFrom(
                          minimumSize: const Size.fromHeight(50),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14),
                          ),
                        ),
                        child: const Text('Back'),
                      ),
                    ),
                    const SizedBox(width: 12),
                  ],
                  Expanded(
                    flex: 2,
                    child: Container(
                      height: 50,
                      decoration: BoxDecoration(
                        gradient: PartnerTheme.saffronGradient,
                        borderRadius: BorderRadius.circular(14),
                        boxShadow: PartnerTheme.glow(PartnerTheme.saffron, blur: 10),
                      ),
                      child: Material(
                        color: Colors.transparent,
                        child: InkWell(
                          borderRadius: BorderRadius.circular(14),
                          onTap: _saving
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
                          child: Center(
                            child: _saving
                                ? const SizedBox(
                                    width: 22,
                                    height: 22,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2,
                                      valueColor: AlwaysStoppedAnimation<Color>(
                                          Colors.white),
                                    ),
                                  )
                                : Text(
                                    _step == 0
                                        ? 'Next: Practice Details'
                                        : 'Submit Application',
                                    style: const TextStyle(
                                      fontWeight: FontWeight.w800,
                                      fontSize: 14,
                                      color: Colors.white,
                                    ),
                                  ),
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }

  Widget _stepIndicator(int number, String title, bool active, bool current) {
    return Column(
      children: [
        Container(
          width: 34,
          height: 34,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            gradient: active ? PartnerTheme.saffronGradient : null,
            color: active ? null : Colors.grey.withValues(alpha: 0.2),
            border: Border.all(
              color: current ? PartnerTheme.gold : Colors.transparent,
              width: 2,
            ),
          ),
          child: Center(
            child: Text(
              '$number',
              style: TextStyle(
                fontWeight: FontWeight.w800,
                color: active ? Colors.white : Colors.grey,
              ),
            ),
          ),
        ),
        const SizedBox(height: 4),
        Text(
          title,
          style: TextStyle(
            fontSize: 11,
            fontWeight: current ? FontWeight.w800 : FontWeight.w500,
            color: current ? PartnerTheme.saffron : Colors.grey,
          ),
        ),
      ],
    );
  }

  Widget _stepOneForm(bool dark) {
    return Form(
      key: _formKeys[0],
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          PartnerCard(
            padding: const EdgeInsets.all(18),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Astrologer Identity',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 14),
                TextFormField(
                  controller: _name,
                  decoration: const InputDecoration(
                    labelText: 'Full Name (e.g. Acharya Rajesh Sharma)',
                    prefixIcon: Icon(Icons.person_outline_rounded),
                  ),
                  validator: (v) =>
                      (v == null || v.trim().isEmpty) ? 'Name is required' : null,
                ),
                const SizedBox(height: 14),
                TextFormField(
                  controller: _email,
                  keyboardType: TextInputType.emailAddress,
                  decoration: const InputDecoration(
                    labelText: 'Email Address',
                    prefixIcon: Icon(Icons.email_outlined),
                  ),
                  validator: (v) => (v == null || !v.contains('@'))
                      ? 'Valid email required'
                      : null,
                ),
                const SizedBox(height: 14),
                TextFormField(
                  controller: _phone,
                  keyboardType: TextInputType.phone,
                  decoration: const InputDecoration(
                    labelText: 'Mobile Phone (+91)',
                    prefixIcon: Icon(Icons.phone_outlined),
                  ),
                  validator: (v) => (v == null || v.trim().length < 8)
                      ? 'Valid phone required'
                      : null,
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),

          // Core Specializations
          PartnerCard(
            padding: const EdgeInsets.all(18),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Astrological Expertise',
                  style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 4),
                Text(
                  'Select your core divination & puja domains',
                  style: TextStyle(
                    fontSize: 12,
                    color: dark ? Colors.white60 : Colors.black54,
                  ),
                ),
                const SizedBox(height: 12),
                Wrap(
                  spacing: 8,
                  runSpacing: 8,
                  children: _availableSkills.map((skill) {
                    final isSel = _selectedSkills.contains(skill);
                    return FilterChip(
                      selected: isSel,
                      label: Text(skill),
                      selectedColor:
                          PartnerTheme.saffron.withValues(alpha: 0.2),
                      checkmarkColor: PartnerTheme.saffron,
                      labelStyle: TextStyle(
                        fontSize: 12,
                        fontWeight: isSel ? FontWeight.w800 : FontWeight.w500,
                        color: isSel ? PartnerTheme.saffron : null,
                      ),
                      onSelected: (val) {
                        setState(() {
                          if (val) {
                            _selectedSkills.add(skill);
                          } else {
                            _selectedSkills.remove(skill);
                          }
                        });
                      },
                    );
                  }).toList(),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _stepTwoForm(bool dark) {
    return Form(
      key: _formKeys[1],
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          PartnerCard(
            padding: const EdgeInsets.all(18),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Practice & Location',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 14),
                TextFormField(
                  controller: _business,
                  decoration: const InputDecoration(
                    labelText: 'Ashram / Temple / Practice Name',
                    prefixIcon: Icon(Icons.temple_hindu_outlined),
                  ),
                  validator: (v) =>
                      (v == null || v.trim().isEmpty) ? 'Required' : null,
                ),
                const SizedBox(height: 14),
                TextFormField(
                  controller: _address,
                  maxLines: 2,
                  decoration: const InputDecoration(
                    labelText: 'Address (City, State, Country)',
                    prefixIcon: Icon(Icons.location_on_outlined),
                  ),
                  validator: (v) =>
                      (v == null || v.trim().isEmpty) ? 'Required' : null,
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),

          PartnerCard(
            padding: const EdgeInsets.all(18),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Account Security Password',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 14),
                TextFormField(
                  controller: _password,
                  obscureText: true,
                  decoration: const InputDecoration(
                    labelText: 'Create Password',
                    prefixIcon: Icon(Icons.lock_outline_rounded),
                  ),
                  validator: (v) => (v == null || v.length < 8)
                      ? 'Minimum 8 characters'
                      : null,
                ),
                const SizedBox(height: 14),
                TextFormField(
                  controller: _confirm,
                  obscureText: true,
                  decoration: const InputDecoration(
                    labelText: 'Confirm Password',
                    prefixIcon: Icon(Icons.lock_reset_rounded),
                  ),
                  validator: (v) =>
                      (v != _password.text) ? 'Passwords do not match' : null,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _successView(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                width: 90,
                height: 90,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: PartnerTheme.luxuryGold,
                  boxShadow: PartnerTheme.glow(PartnerTheme.gold, blur: 20),
                ),
                child: const Icon(
                  Icons.verified_rounded,
                  size: 50,
                  color: Colors.white,
                ),
              ),
              const SizedBox(height: 24),
              const Text(
                'Application Received! 🙏',
                style: TextStyle(fontSize: 22, fontWeight: FontWeight.w900),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 10),
              Text(
                'Our Vedic Onboarding panel reviews credentials within 24 hours. Once verified, you will be notified via SMS and can start taking live consultations.',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 13.5,
                  height: 1.5,
                  color: dark ? Colors.white60 : Colors.black54,
                ),
              ),
              const SizedBox(height: 28),
              Container(
                height: 48,
                decoration: BoxDecoration(
                  gradient: PartnerTheme.saffronGradient,
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Material(
                  color: Colors.transparent,
                  child: InkWell(
                    borderRadius: BorderRadius.circular(14),
                    onTap: () => Navigator.of(context).pop(),
                    child: const Padding(
                      padding: EdgeInsets.symmetric(horizontal: 28),
                      child: Center(
                        child: Text(
                          'Back to Login',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w800,
                            color: Colors.white,
                          ),
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
    );
  }
}
