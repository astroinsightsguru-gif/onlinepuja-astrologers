import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../state/app_session.dart';

import '../puja/puja_list_screen.dart';

/// Doorstep Prasadam GPS & Spiritual Journey Tracker screen.
class PrasadamTrackerScreen extends StatefulWidget {
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

  @override
  State<PrasadamTrackerScreen> createState() => _PrasadamTrackerScreenState();
}

class _PrasadamTrackerScreenState extends State<PrasadamTrackerScreen> {
  bool _loading = false;
  String? _orderId;
  String? _pujaName;
  String? _temple;
  String? _trackingCode;
  String? _courierName;
  int _currentStep = 3;
  bool _hasOrder = false;

  @override
  void initState() {
    super.initState();
    _initOrder();
  }

  Future<void> _initOrder() async {
    if (widget.orderId != null && widget.orderId!.isNotEmpty) {
      setState(() {
        _orderId = widget.orderId;
        _pujaName = widget.pujaName ?? 'Sacred Vedic Puja';
        _temple = widget.temple ?? 'Holy Teerth Mandir';
        _trackingCode = widget.trackingCode;
        _courierName = widget.courierName ?? 'Sanctum Priority Express';
        _currentStep = widget.currentStep;
        _hasOrder = true;
      });
      return;
    }

    setState(() => _loading = true);
    try {
      final uid = SessionStore.instance.user?.id;
      final decoded = await ApiClient.instance.post('/getUserPujaOrders', body: {
        if (uid != null && uid > 0) 'userId': uid,
      });
      List<dynamic> list = [];
      if (decoded is Map<String, dynamic> && decoded['recordList'] is List) {
        list = decoded['recordList'];
      }
      if (list.isNotEmpty) {
        final latest = list.first as Map<String, dynamic>;
        final id = latest['id']?.toString() ?? '1';
        final name = latest['puja_title']?.toString() ?? latest['pujaName']?.toString() ?? 'Vedic Puja';
        final temple = latest['puja_place']?.toString() ?? 'Sanctum Mandir';
        final awb = latest['awb_number']?.toString() ?? latest['tracking_code']?.toString();
        final courier = latest['courier_name']?.toString() ?? 'Sanctum Priority Express';
        final status = latest['order_status']?.toString() ?? latest['puja_order_status']?.toString() ?? '';
        int step = 2;
        if (status.toLowerCase().contains('complete') || status.toLowerCase().contains('deliver')) {
          step = 4;
        } else if (status.toLowerCase().contains('transit') || status.toLowerCase().contains('dispatch')) {
          step = 3;
        }

        if (mounted) {
          setState(() {
            _orderId = 'OP-PUJA-$id';
            _pujaName = name;
            _temple = temple;
            _trackingCode = awb;
            _courierName = courier;
            _currentStep = step;
            _hasOrder = true;
            _loading = false;
          });
        }
        return;
      }
    } catch (_) {}

    if (mounted) {
      setState(() {
        _loading = false;
        _hasOrder = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;

    if (_loading) {
      return Scaffold(
        appBar: AppBar(title: const Text('Doorstep Prasadam Tracker')),
        body: const Center(child: CircularProgressIndicator()),
      );
    }

    if (!_hasOrder) {
      return Scaffold(
        appBar: AppBar(title: const Text('Doorstep Prasadam Tracker')),
        body: Center(
          child: Padding(
            padding: const EdgeInsets.all(28),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  padding: const EdgeInsets.all(22),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFEF3C7),
                    shape: BoxShape.circle,
                    border: Border.all(color: const Color(0xFFFDE68A), width: 2),
                  ),
                  child: const Icon(
                    Icons.local_shipping_outlined,
                    size: 52,
                    color: Color(0xFFD97706),
                  ),
                ),
                const SizedBox(height: 20),
                const Text(
                  'No Active Prasad Deliveries',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF1E293B),
                  ),
                ),
                const SizedBox(height: 8),
                const Text(
                  'When you participate in holy temple pujas on OnlinePuja.live, your sanctified Prasad box and sacred raksha sutra are tracked live here from the sanctum to your home.',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 13,
                    color: Color(0xFF64748B),
                    height: 1.4,
                  ),
                ),
                const SizedBox(height: 24),
                FilledButton.icon(
                  style: FilledButton.styleFrom(
                    backgroundColor: const Color(0xFFD97706),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 12),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                  onPressed: () {
                    Navigator.of(context).pushNamed(PujaListScreen.route);
                  },
                  icon: const Icon(Icons.temple_hindu_rounded, size: 18),
                  label: const Text(
                    'Book a Sacred Temple Puja',
                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                  ),
                ),
              ],
            ),
          ),
        ),
      );
    }

    final displayOrderId = _orderId ?? 'OP-PUJA';
    final displayPujaName = _pujaName ?? 'Vedic Puja';
    final displayTemple = _temple ?? 'Temple Sanctum';
    final displayTracking = _trackingCode ?? 'Tracking Pending';
    final displayCourier = _courierName ?? 'Sanctum Priority Express';
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
                          'Order #$displayOrderId',
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
                    displayPujaName,
                    style: const TextStyle(color: Colors.white, fontSize: 17, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      const Icon(Icons.temple_hindu_rounded, color: Colors.white70, size: 16),
                      const SizedBox(width: 6),
                      Expanded(
                        child: Text(
                          displayTemple,
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
              subtitle: 'Priest completed your personalized Sankalp with Vedic chants calling your name & gotra.',
              time: 'Completed',
              completed: _currentStep >= 1,
              isCurrent: _currentStep == 1,
              isFirst: true,
              actionWidget: Container(
                margin: const EdgeInsets.only(top: 8),
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                decoration: BoxDecoration(
                  color: const Color(0xFFFEF3C7),
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: const Color(0xFFF59E0B)),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(Icons.play_circle_fill_rounded, size: 16, color: Color(0xFFD97706)),
                    const SizedBox(width: 6),
                    const Text(
                      'Sankalp Video Proof Available',
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF92400E),
                      ),
                    ),
                    const SizedBox(width: 8),
                    TextButton(
                      style: TextButton.styleFrom(
                        padding: EdgeInsets.zero,
                        minimumSize: Size.zero,
                        tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                      ),
                      onPressed: () {
                        Navigator.pushNamed(context, '/sankalp-vault');
                      },
                      child: const Text(
                        'Watch Proof →',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w900,
                          color: Color(0xFFB45309),
                          decoration: TextDecoration.underline,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            _timelineStep(
              context,
              step: 2,
              title: 'Prasadam Consecrated & Blessed',
              subtitle: 'Sacred Bhasma, Rudraksha, and Holy Dry Fruits sanctified at deity altar.',
              time: 'Consecrated',
              completed: _currentStep >= 2,
              isCurrent: _currentStep == 2,
            ),
            _timelineStep(
              context,
              step: 3,
              title: 'Dispatched via $displayCourier',
              subtitle: 'AWB Code: $displayTracking. In transit to your local delivery hub.',
              time: 'In Transit',
              completed: _currentStep >= 3,
              isCurrent: _currentStep == 3,
            ),
            _timelineStep(
              context,
              step: 4,
              title: 'Doorstep Delivery & Family Blessing',
              subtitle: 'Expected arrival in 2-3 business days. Delivery agent will call before arrival.',
              time: 'Estimated Soon',
              completed: _currentStep >= 4,
              isCurrent: _currentStep == 4,
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
                final uri = Uri.parse('https://wa.me/$wa?text=Namaste,%20I%20have%20an%20inquiry%20regarding%20my%20Prasadam%20Order%20$displayOrderId');
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
    Widget? actionWidget,
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
                ?actionWidget,
              ],
            ),
          ),
        ),
      ],
    );
  }
}
