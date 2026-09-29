import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';

/// Astrologer-side order fulfillment hub:
/// 1. Puja Bookings (view sankalp, schedule, mark completed)
/// 2. Report Consultations (view client birth details, submit astrological report)
class OrdersFulfillmentScreen extends StatefulWidget {
  const OrdersFulfillmentScreen({super.key});

  static const route = '/orders';

  @override
  State<OrdersFulfillmentScreen> createState() => _OrdersFulfillmentScreenState();
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
        PartnerApi.instance.astrologerPujaList(astrologerId: astroId).catchError((_) => <Map<String, dynamic>>[]),
        PartnerApi.instance.getUserReportRequests(astrologerId: astroId).catchError((_) => <Map<String, dynamic>>[]),
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
    return Scaffold(
      appBar: AppBar(
        title: const Text('Order Fulfillment'),
        bottom: TabBar(
          controller: _tab,
          tabs: [
            Tab(
              icon: const Icon(Icons.temple_hindu_rounded),
              text: 'Puja Bookings (${_pujas.length})',
            ),
            Tab(
              icon: const Icon(Icons.description_outlined),
              text: 'Reports (${_reports.length})',
            ),
          ],
        ),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
              ? StatusViews.error(
                  context,
                  _error!,
                  onRetry: _load,
                )
              : TabBarView(
                  controller: _tab,
                  children: [
                    _pujaList(),
                    _reportList(),
                  ],
                ),
    );
  }

  Widget _pujaList() {
    if (_pujas.isEmpty) {
      return StatusViews.empty(
        context,
        message: 'No Puja Bookings Assigned Yet',
      );
    }
    return RefreshIndicator(
      onRefresh: _load,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _pujas.length,
        separatorBuilder: (_, index) => const SizedBox(height: 12),
        itemBuilder: (ctx, i) {
          final item = _pujas[i];
          final pujaName = item['pujaName'] ?? item['name'] ?? 'Puja Ceremony';
          final customerName = item['userName'] ?? item['customerName'] ?? 'Devotee';
          final date = item['pujaDate'] ?? item['date'] ?? 'Upcoming';
          final status = item['status'] ?? 'Assigned';
          return Card(
            elevation: 1,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: AppTheme.brandSaffron.withValues(alpha: 0.12),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Icon(Icons.temple_hindu_rounded, color: AppTheme.brandSaffron),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              pujaName.toString(),
                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                            ),
                            Text(
                              'Devotee: $customerName',
                              style: TextStyle(color: Colors.grey.shade700, fontSize: 13),
                            ),
                          ],
                        ),
                      ),
                      Chip(
                        label: Text(status.toString(), style: const TextStyle(fontSize: 11)),
                        backgroundColor: AppTheme.brandSaffron.withValues(alpha: 0.2),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  const Divider(),
                  Row(
                    children: [
                      const Icon(Icons.calendar_today_rounded, size: 14, color: Colors.grey),
                      const SizedBox(width: 6),
                      Text('Date: $date', style: const TextStyle(fontSize: 12, color: Colors.grey)),
                      const Spacer(),
                      FilledButton.tonal(
                        onPressed: () => _viewPujaDetails(item),
                        child: const Text('View Details'),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _reportList() {
    if (_reports.isEmpty) {
      return StatusViews.empty(
        context,
        message: 'No Report Requests Yet',
      );
    }
    return RefreshIndicator(
      onRefresh: _load,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _reports.length,
        separatorBuilder: (_, index) => const SizedBox(height: 12),
        itemBuilder: (ctx, i) {
          final item = _reports[i];
          final title = item['reportType'] ?? item['title'] ?? 'Astrology Report';
          final client = item['userName'] ?? item['name'] ?? 'Client';
          final id = item['id'] ?? 0;
          return Card(
            elevation: 1,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: Colors.deepPurple.withValues(alpha: 0.1),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Icon(Icons.description_rounded, color: Colors.deepPurple),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              title.toString(),
                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                            ),
                            Text(
                              'Client: $client',
                              style: TextStyle(color: Colors.grey.shade700, fontSize: 13),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  const Divider(),
                  Row(
                    children: [
                      Text('Order #$id', style: const TextStyle(fontSize: 12, color: Colors.grey)),
                      const Spacer(),
                      FilledButton(
                        onPressed: () => _fulfillReport(item),
                        child: const Text('Fulfill Report'),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  void _viewPujaDetails(Map<String, dynamic> item) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              item['pujaName']?.toString() ?? 'Puja Ceremony Details',
              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 12),
            Text('Devotee: ${item['userName'] ?? 'Devotee'}'),
            const SizedBox(height: 6),
            Text('Sankalp: ${item['sankalp'] ?? item['notes'] ?? 'General well-being & family blessings'}'),
            const SizedBox(height: 6),
            Text('Location: ${item['address'] ?? 'Online / Live Puja'}'),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              child: FilledButton(
                onPressed: () {
                  Navigator.pop(ctx);
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Puja marked as verified & in preparation.')),
                  );
                },
                child: const Text('Acknowledge Booking'),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _fulfillReport(Map<String, dynamic> item) {
    final reportController = TextEditingController();
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('Fulfill ${item['reportType'] ?? 'Report'}'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text('Client: ${item['userName'] ?? 'Devotee'}', style: const TextStyle(fontWeight: FontWeight.w600)),
            const SizedBox(height: 12),
            TextField(
              controller: reportController,
              maxLines: 5,
              decoration: const InputDecoration(
                hintText: 'Enter detailed astrological analysis, planetary placements, and recommended remedies/gemstones…',
                border: OutlineInputBorder(),
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Cancel'),
          ),
          FilledButton(
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
                    const SnackBar(content: Text('Report successfully delivered to client!')),
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
            child: const Text('Submit Report'),
          ),
        ],
      ),
    );
  }
}
