import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../../theme/partner_theme.dart';
import '../../widgets/partner_widgets.dart';

/// Astrologer-side order fulfillment hub:
/// 1. Puja Bookings (view devotee sankalp, temple, schedule, mark completed)
/// 2. Report Consultations (view client birth details, submit astrological report)
class OrdersFulfillmentScreen extends StatefulWidget {
  const OrdersFulfillmentScreen({super.key});

  static const route = '/orders';

  @override
  State<OrdersFulfillmentScreen> createState() =>
      _OrdersFulfillmentScreenState();
}

class _OrdersFulfillmentScreenState extends State<OrdersFulfillmentScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tab;
  bool _loading = true;
  List<Map<String, dynamic>> _pujas = [];
  List<Map<String, dynamic>> _reports = [];
  Object? _error;

  @override
  void initState() {
    super.initState();
    _tab = TabController(length: 2, vsync: this);
    _load();
  }

  @override
  void dispose() {
    _tab.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    final session = context.read<PartnerSession>();
    final astroId = session.astrologerId;
    try {
      final results = await Future.wait([
        PartnerApi.instance
            .astrologerPujaList(astrologerId: astroId)
            .catchError((_) => <Map<String, dynamic>>[]),
        PartnerApi.instance
            .getUserReportRequests(astrologerId: astroId)
            .catchError((_) => <Map<String, dynamic>>[]),
      ]);
      if (mounted) {
        setState(() {
          _pujas = results[0];
          _reports = results[1];
          _loading = false;
        });
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _error = e;
          _loading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.orderFulfillmentTitle,
            style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 18)),
        bottom: TabBar(
          controller: _tab,
          indicatorColor: PartnerTheme.saffron,
          labelColor: PartnerTheme.saffron,
          unselectedLabelColor: dark ? Colors.white60 : Colors.black54,
          labelStyle:
              const TextStyle(fontWeight: FontWeight.w800, fontSize: 13),
          tabs: [
            Tab(
              icon: const Icon(Icons.temple_hindu_rounded, size: 20),
              text: '${AppStrings.pujaRitualsTab} (${_pujas.length})',
            ),
            Tab(
              icon: const Icon(Icons.description_rounded, size: 20),
              text: '${AppStrings.reportsTab} (${_reports.length})',
            ),
          ],
        ),
      ),
      body: _loading
          ? const Center(
              child: CircularProgressIndicator(color: PartnerTheme.saffron))
          : _error != null
              ? StatusViews.error(
                  context,
                  _error!,
                  onRetry: _load,
                )
              : TabBarView(
                  controller: _tab,
                  children: [
                    _pujaList(dark),
                    _reportList(dark),
                  ],
                ),
    );
  }

  Widget _pujaList(bool dark) {
    if (_pujas.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(28),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: PartnerTheme.saffron.withValues(alpha: 0.12),
                ),
                child: const Icon(Icons.temple_hindu_rounded,
                    size: 46, color: PartnerTheme.saffron),
              ),
              const SizedBox(height: 18),
              Text(
                AppStrings.noPujasScheduled,
                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
              ),
              const SizedBox(height: 6),
              Text(
                'New devotee Sankalps and temple rituals assigned to you will appear here with devotee details.',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 13,
                  color: dark ? Colors.white60 : Colors.black54,
                  height: 1.4,
                ),
              ),
            ],
          ),
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _load,
      child: ListView.separated(
        padding: const EdgeInsets.fromLTRB(16, 14, 16, 28),
        itemCount: _pujas.length,
        separatorBuilder: (_, _) => const SizedBox(height: 14),
        itemBuilder: (ctx, i) {
          final item = _pujas[i];
          final pujaName =
              item['pujaName'] ?? item['name'] ?? 'Vedic Puja Ceremony';
          final customerName =
              item['userName'] ?? item['customerName'] ?? 'Devotee';
          final date = item['pujaDate'] ?? item['date'] ?? 'Upcoming Muhurat';
          final status = (item['status'] ?? 'Assigned').toString();
          final isDone = status.toLowerCase() == 'completed';

          return PartnerCard(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        gradient: PartnerTheme.saffronGradient,
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: const Icon(Icons.temple_hindu_rounded,
                          color: Colors.white, size: 24),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            pujaName.toString(),
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                          const SizedBox(height: 3),
                          Text(
                            '${AppStrings.devotee}: $customerName',
                            style: TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                              color: dark ? Colors.white70 : Colors.black87,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: isDone
                            ? PartnerTheme.emerald.withValues(alpha: 0.15)
                            : PartnerTheme.amber.withValues(alpha: 0.15),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        status.toUpperCase(),
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.w800,
                          color: isDone
                              ? PartnerTheme.emerald
                              : PartnerTheme.amber,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                const Divider(height: 1),
                const SizedBox(height: 12),
                Row(
                  children: [
                    const Icon(Icons.event_available_rounded,
                        size: 15, color: PartnerTheme.saffron),
                    const SizedBox(width: 6),
                    Expanded(
                      child: Text(
                        date,
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: dark ? Colors.white60 : Colors.black54,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    const SizedBox(width: 8),
                    FilledButton.tonal(
                      onPressed: () => _viewPujaDetails(item, dark),
                      style: FilledButton.styleFrom(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 14, vertical: 8),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                      ),
                      child: Text(AppStrings.viewSankalp,
                          style: const TextStyle(
                              fontSize: 12, fontWeight: FontWeight.w700)),
                    ),
                  ],
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _reportList(bool dark) {
    if (_reports.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(28),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: PartnerTheme.purple.withValues(alpha: 0.12),
                ),
                child: const Icon(Icons.description_rounded,
                    size: 46, color: PartnerTheme.purple),
              ),
              const SizedBox(height: 18),
              Text(
                AppStrings.noReportsPending,
                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
              ),
              const SizedBox(height: 6),
              Text(
                'In-depth astrological report inquiries (Kundli Milan, Sade Sati, Gemology) requested by clients will be listed here.',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 13,
                  color: dark ? Colors.white60 : Colors.black54,
                  height: 1.4,
                ),
              ),
            ],
          ),
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _load,
      child: ListView.separated(
        padding: const EdgeInsets.fromLTRB(16, 14, 16, 28),
        itemCount: _reports.length,
        separatorBuilder: (_, _) => const SizedBox(height: 14),
        itemBuilder: (ctx, i) {
          final item = _reports[i];
          final title =
              item['reportType'] ?? item['title'] ?? 'Astrological Kundli Report';
          final client = item['userName'] ?? item['name'] ?? 'Devotee';
          final id = item['id'] ?? 0;

          return PartnerCard(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFF8B5CF6), Color(0xFF6D28D9)],
                        ),
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: const Icon(Icons.description_rounded,
                          color: Colors.white, size: 24),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            title.toString(),
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                          const SizedBox(height: 3),
                          Text(
                            'Client: $client',
                            style: TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                              color: dark ? Colors.white70 : Colors.black87,
                            ),
                          ),
                        ],
                      ),
                    ),
                    IconButton(
                      tooltip: 'Devotee Kundli',
                      icon: const Icon(Icons.auto_awesome,
                          color: PartnerTheme.gold, size: 20),
                      onPressed: () => DevoteeKundliSheet.show(
                        context,
                        devoteeName: client.toString(),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                const Divider(height: 1),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(
                        color: dark ? Colors.white10 : Colors.black.withValues(alpha: 0.05),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        'Order #$id',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                          color: dark ? Colors.white60 : Colors.black54,
                        ),
                      ),
                    ),
                    const Spacer(),
                    Container(
                      height: 36,
                      decoration: BoxDecoration(
                        gradient: PartnerTheme.saffronGradient,
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Material(
                        color: Colors.transparent,
                        child: InkWell(
                          borderRadius: BorderRadius.circular(10),
                          onTap: () => _fulfillReport(item, dark),
                          child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 14),
                            child: Center(
                              child: Row(
                                children: [
                                  const Icon(Icons.edit_note_rounded,
                                      size: 16, color: Colors.white),
                                  const SizedBox(width: 4),
                                  Text(
                                    AppStrings.draftSubmitReport,
                                    style: const TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.w800,
                                      color: Colors.white,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  void _viewPujaDetails(Map<String, dynamic> item, bool dark) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => Container(
        padding: const EdgeInsets.fromLTRB(20, 16, 20, 28),
        decoration: BoxDecoration(
          color: dark ? PartnerTheme.darkSurface : Colors.white,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(28)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(
              child: Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: Colors.grey.withValues(alpha: 0.4),
                  borderRadius: BorderRadius.circular(4),
                ),
              ),
            ),
            const SizedBox(height: 16),
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    gradient: PartnerTheme.saffronGradient,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(Icons.temple_hindu_rounded,
                      color: Colors.white, size: 22),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        item['pujaName']?.toString() ?? 'Puja Ceremony Details',
                        style: const TextStyle(
                            fontSize: 18, fontWeight: FontWeight.w800),
                      ),
                      Text(
                        'Devotee: ${item['userName'] ?? 'Devotee'}',
                        style: TextStyle(
                            fontSize: 12.5,
                            color: dark ? Colors.white60 : Colors.black54),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 18),
            PartnerCard(
              padding: const EdgeInsets.all(14),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Devotee Sankalp & Wishes',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w800,
                      color: PartnerTheme.saffron,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    item['sankalp']?.toString() ??
                        item['notes']?.toString() ??
                        'Welfare of family, peace, and health blessings with Mahamrityunjaya Mantra.',
                    style: const TextStyle(fontSize: 13, height: 1.4),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),
            PartnerCard(
              padding: const EdgeInsets.all(14),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Ritual Location & Logistics',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w800,
                      color: PartnerTheme.amber,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    item['address']?.toString() ??
                        'Online Live Stream · Acharya Sanctum / Temple',
                    style: const TextStyle(fontSize: 13),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),
            Container(
              height: 48,
              decoration: BoxDecoration(
                gradient: PartnerTheme.emeraldGradient,
                borderRadius: BorderRadius.circular(14),
                boxShadow: PartnerTheme.glow(PartnerTheme.emerald, blur: 8),
              ),
              child: Material(
                color: Colors.transparent,
                child: InkWell(
                  borderRadius: BorderRadius.circular(14),
                  onTap: () {
                    Navigator.pop(ctx);
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Text(
                            'Puja marked as verified & scheduled in your ritual calendar!'),
                      ),
                    );
                  },
                  child: const Center(
                    child: Text(
                      'Acknowledge & Confirm Booking',
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
          ],
        ),
      ),
    );
  }

  void _fulfillReport(Map<String, dynamic> item, bool dark) {
    final reportController = TextEditingController();
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text(
          'Fulfill ${item['reportType'] ?? 'Report'}',
          style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 18),
        ),
        content: SizedBox(
          width: double.maxFinite,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Client: ${item['userName'] ?? 'Devotee'}',
                style: const TextStyle(fontWeight: FontWeight.w700),
              ),
              const SizedBox(height: 8),
              Text(
                'Write your thorough astrological horoscope findings, planetary Dasha interpretations, and Vedic remedies below:',
                style: TextStyle(
                  fontSize: 11.5,
                  color: dark ? Colors.white60 : Colors.black54,
                ),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: reportController,
                maxLines: 6,
                decoration: InputDecoration(
                  hintText:
                      '1. Planetary Placements & Lagna Analysis...\n2. Current Mahadasha & Antardasha Impact...\n3. Recommended Remedies, Mantras & Gemstones...',
                  hintStyle: TextStyle(
                    fontSize: 12,
                    color: Colors.grey.withValues(alpha: 0.6),
                  ),
                  filled: true,
                  fillColor: dark ? PartnerTheme.darkSurface : const Color(0xFFF7F4ED),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                ),
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Cancel'),
          ),
          FilledButton(
            style: FilledButton.styleFrom(
              backgroundColor: PartnerTheme.saffron,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
            onPressed: () async {
              final text = reportController.text.trim();
              if (text.isEmpty) return;
              Navigator.pop(ctx);
              try {
                final id = int.tryParse(item['id']?.toString() ?? '') ?? 0;
                await PartnerApi.instance.submitUserReport(
                  reportId: id,
                  reportText: text,
                );
                if (mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content:
                          Text('Astrological Report successfully delivered to devotee!'),
                    ),
                  );
                  _load();
                }
              } catch (e) {
                if (mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text('Failed to submit report: $e')),
                  );
                }
              }
            },
            child: const Text('Deliver Report',
                style: TextStyle(fontWeight: FontWeight.w800)),
          ),
        ],
      ),
    );
  }
}
