import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../state/app_session.dart';

/// Doorstep Prasadam GPS & Spiritual Journey Tracker screen.
class PrasadamTrackerScreen extends StatelessWidget {
  const PrasadamTrackerScreen({
    super.key,
    this.orderId,
    this.pujaName,
    this.temple,
    this.trackingCode,
    this.courierName,
    this.currentStep = 3,
  });

  static const route = '/prasadam-tracker';

  final String? orderId;
  final String? pujaName;
  final String? temple;
  final String? trackingCode;
  final String? courierName;
  final int currentStep;

  String get _displayOrderId => orderId ?? 'OP-PUJA-${DateTime.now().year}88';
  String get _displayPujaName => pujaName ?? 'Maha Mrityunjaya Special Temple Puja';
  String get _displayTemple => temple ?? 'Kashi Vishwanath Temple, Varanasi';
  String get _displayTracking => trackingCode ?? 'BD-${_displayOrderId.replaceAll(RegExp(r'[^0-9]'), '')}94';
  String get _displayCourier => courierName ?? 'BlueDart Express';

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Doorstep Prasadam Tracker'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Hero Card
            Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [AppTheme.brandDeep, AppTheme.brandDeep.withValues(alpha: 0.85)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: AppTheme.brandSaffron.withValues(alpha: 0.4)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: AppTheme.brandSaffron.withValues(alpha: 0.2),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: AppTheme.brandSaffron.withValues(alpha: 0.4)),
                        ),
                        child: Text(
                          'Order #$_displayOrderId',
                          style: const TextStyle(color: AppTheme.brandGold, fontSize: 12, fontWeight: FontWeight.bold),
                        ),
                      ),
                      const Spacer(),
                      const Icon(Icons.verified, color: Colors.greenAccent, size: 18),
                      const SizedBox(width: 4),
                      const Text(
                        'Sanctified',
                        style: TextStyle(color: Colors.greenAccent, fontSize: 12, fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Text(
                    _displayPujaName,
                    style: const TextStyle(color: Colors.white, fontSize: 17, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      const Icon(Icons.temple_hindu_rounded, color: Colors.white70, size: 16),
                      const SizedBox(width: 6),
                      Expanded(
                        child: Text(
                          _displayTemple,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(color: Colors.white70, fontSize: 13),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            Text(
              'Sacred Journey & Timeline',
              style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 14),

            // Timeline Steps
            _timelineStep(
              context,
              step: 1,
              title: 'Sankalp & Ritual Completed',
              subtitle: 'Priest completed your personalized Sankalp with Vedic chants at sanctum.',
              time: 'Completed',
              completed: currentStep >= 1,
              isCurrent: currentStep == 1,
              isFirst: true,
            ),
            _timelineStep(
              context,
              step: 2,
              title: 'Prasadam Consecrated & Blessed',
              subtitle: 'Sacred Bhasma, Rudraksha, and Holy Dry Fruits sanctified at deity altar.',
              time: 'Consecrated',
              completed: currentStep >= 2,
              isCurrent: currentStep == 2,
            ),
            _timelineStep(
              context,
              step: 3,
              title: 'Dispatched via $_displayCourier',
              subtitle: 'AWB Code: $_displayTracking. In transit to your local delivery hub.',
              time: 'In Transit',
              completed: currentStep >= 3,
              isCurrent: currentStep == 3,
            ),
            _timelineStep(
              context,
              step: 4,
              title: 'Doorstep Delivery & Family Blessing',
              subtitle: 'Expected arrival in 2-3 business days. Delivery agent will call before arrival.',
              time: 'Estimated Soon',
              completed: currentStep >= 4,
              isCurrent: currentStep == 4,
              isLast: true,
            ),

            const SizedBox(height: 24),

            // Sacred Unboxing Instructions
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: scheme.surfaceContainerHighest.withValues(alpha: 0.4),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: scheme.outline.withValues(alpha: 0.2)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      const Text('🪔', style: TextStyle(fontSize: 20)),
                      const SizedBox(width: 8),
                      Text(
                        'Prasadam Consumption Ritual',
                        style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    '1. Receive the parcel after morning purification with clean hands.\n'
                    '2. Place the sacred packet before your home mandir or deity picture.\n'
                    '3. Light a diya or incense, chant Om Namah Shivaya 3 times, and partake in the blessed prasadam with family.',
                    style: TextStyle(fontSize: 12.5, height: 1.5),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // Sanctum Support Button
            OutlinedButton.icon(
              style: OutlinedButton.styleFrom(
                minimumSize: const Size.fromHeight(48),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              icon: const Icon(Icons.headset_mic_rounded),
              label: const Text('Have Questions? Contact Sanctum Support'),
              onPressed: () async {
                final wa = context.read<AppSession>().flags.supportWhatsapp;
                final phone = context.read<AppSession>().flags.supportPhone;
                final uri = Uri.parse('https://wa.me/$wa?text=Namaste,%20I%20have%20an%20inquiry%20regarding%20my%20Prasadam%20Order%20$_displayOrderId');
                try {
                  await launchUrl(uri, mode: LaunchMode.externalApplication);
                } catch (_) {
                  if (context.mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text('Support helpline: $phone')),
                    );
                  }
                }
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _timelineStep(
    BuildContext context, {
    required int step,
    required String title,
    required String subtitle,
    required String time,
    required bool completed,
    bool isCurrent = false,
    bool isFirst = false,
    bool isLast = false,
  }) {
    final scheme = Theme.of(context).colorScheme;

    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Column(
          children: [
            Container(
              width: 32,
              height: 32,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: completed
                    ? (isCurrent ? AppTheme.brandSaffron : Colors.green)
                    : scheme.outline.withValues(alpha: 0.2),
                border: Border.all(
                  color: isCurrent ? AppTheme.brandGold : Colors.transparent,
                  width: 2,
                ),
              ),
              child: Center(
                child: completed
                    ? (isCurrent
                        ? const Icon(Icons.local_shipping, size: 16, color: Colors.white)
                        : const Icon(Icons.check, size: 16, color: Colors.white))
                    : Text(
                        '$step',
                        style: TextStyle(color: scheme.outline, fontWeight: FontWeight.bold, fontSize: 12),
                      ),
              ),
            ),
            if (!isLast)
              Container(
                width: 2,
                height: 48,
                color: completed ? Colors.green.withValues(alpha: 0.6) : scheme.outline.withValues(alpha: 0.2),
              ),
          ],
        ),
        const SizedBox(width: 14),
        Expanded(
          child: Padding(
            padding: const EdgeInsets.only(bottom: 22),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Expanded(
                      child: Text(
                        title,
                        style: TextStyle(
                          fontWeight: isCurrent ? FontWeight.w800 : FontWeight.w600,
                          fontSize: 14,
                          color: isCurrent ? scheme.primary : scheme.onSurface,
                        ),
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                      decoration: BoxDecoration(
                        color: completed ? Colors.green.withValues(alpha: 0.1) : scheme.surfaceContainerHighest,
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        time,
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                          color: completed ? Colors.green.shade800 : scheme.outline,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 4),
                Text(
                  subtitle,
                  style: TextStyle(fontSize: 12, color: scheme.outline, height: 1.3),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
