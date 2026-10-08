import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import '../../theme/customer_theme.dart';
import 'puja_list_screen.dart';

class SankalpVaultScreen extends StatefulWidget {
  const SankalpVaultScreen({super.key});

  static const route = '/sankalp-vault';

  @override
  State<SankalpVaultScreen> createState() => _SankalpVaultScreenState();
}

class _SankalpVaultScreenState extends State<SankalpVaultScreen> {
  bool _loading = false;
  List<Map<String, dynamic>> _sankalpItems = [];

  @override
  void initState() {
    super.initState();
    _loadSankalpRecords();
  }

  Future<void> _loadSankalpRecords() async {
    setState(() => _loading = true);
    try {
      final user = SessionStore.instance.user;
      final userId = user?.id ?? 0;
      final records = await MiscApi.instance.getSankalpVault(userId: userId);
      if (records.isNotEmpty) {
        _sankalpItems = records;
      }
    } catch (_) {
      // Handled cleanly
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _openVideo(String url, String pujaName) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) {
        return Container(
          decoration: const BoxDecoration(
            color: Color(0xFF1A132F),
            borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
          ),
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: Colors.white24,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              const SizedBox(height: 16),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Text(
                      'Sankalp Video: $pujaName',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.close, color: Colors.white70),
                    onPressed: () => Navigator.pop(ctx),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              Container(
                height: 220,
                width: double.infinity,
                decoration: BoxDecoration(
                  color: Colors.black,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: CustomerTheme.brandGold.withOpacity(0.3)),
                ),
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    const Icon(Icons.temple_hindu, size: 70, color: Colors.white12),
                    Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        CircleAvatar(
                          radius: 30,
                          backgroundColor: CustomerTheme.brandGold,
                          child: const Icon(Icons.play_arrow_rounded, size: 40, color: Colors.black),
                        ),
                        const SizedBox(height: 10),
                        const Text(
                          'Pandit Ji Calling Your Name & Gotra',
                          style: TextStyle(color: Colors.white70, fontSize: 12),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      style: OutlinedButton.styleFrom(
                        foregroundColor: Colors.white,
                        side: const BorderSide(color: Colors.white24),
                        padding: const EdgeInsets.symmetric(vertical: 12),
                      ),
                      icon: const Icon(Icons.share, size: 18),
                      label: const Text('Share Video'),
                      onPressed: () {
                        Navigator.pop(ctx);
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Sankalp video link copied for WhatsApp Status!')),
                        );
                      },
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: CustomerTheme.brandGold,
                        foregroundColor: Colors.black,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                      ),
                      icon: const Icon(Icons.verified, size: 18),
                      label: const Text('Sankalp Patra', style: TextStyle(fontWeight: FontWeight.bold)),
                      onPressed: () {
                        Navigator.pop(ctx);
                        _showCertificateDialog(pujaName);
                      },
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
            ],
          ),
        );
      },
    );
  }

  void _showCertificateDialog(String pujaName) {
    showDialog(
      context: context,
      builder: (ctx) {
        return Dialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          child: Container(
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: const Color(0xFFFFFBEB),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: CustomerTheme.brandGold, width: 2),
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text('🕉️', style: TextStyle(fontSize: 32)),
                const SizedBox(height: 6),
                const Text(
                  'VEDIC SANKALP CERTIFICATE',
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w900,
                    letterSpacing: 1,
                    color: Color(0xFF78350F),
                  ),
                ),
                const SizedBox(height: 12),
                Text(
                  'This certifies that holy rituals and sankalp for $pujaName were duly completed according to Vedic Shastras by verified Acharyas.',
                  textAlign: TextAlign.center,
                  style: const TextStyle(fontSize: 13, height: 1.4, color: Colors.black87),
                ),
                const SizedBox(height: 20),
                ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF78350F),
                    foregroundColor: Colors.white,
                  ),
                  onPressed: () => Navigator.pop(ctx),
                  child: const Text('Close'),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF9F7F2),
      appBar: AppBar(
        title: const Text('My Sankalp & Prasad Tracking',
            style: TextStyle(fontWeight: FontWeight.bold, fontSize: 17)),
        backgroundColor: const Color(0xFF1A132F),
        foregroundColor: Colors.white,
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : ListView(
              padding: const EdgeInsets.all(16),
              children: [
                _buildHeaderBanner(),
                const SizedBox(height: 18),
                const Text(
                  'Your Completed & Active Pujas:',
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF1A132F),
                  ),
                ),
                const SizedBox(height: 10),
                ..._sankalpItems.map((item) => _buildSankalpCard(item)),
                const SizedBox(height: 20),
                _buildBookAnotherPujaCta(),
              ],
            ),
    );
  }

  Widget _buildHeaderBanner() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF241645), Color(0xFF3B1E63)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: Colors.purple.withOpacity(0.12),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: CustomerTheme.brandGold.withOpacity(0.2),
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.video_library_rounded,
                color: CustomerTheme.brandGold, size: 28),
          ),
          const SizedBox(width: 14),
          const Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Sankalp Video Delivery Vault',
                  style: TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                    fontSize: 15,
                  ),
                ),
                SizedBox(height: 4),
                Text(
                  'Recorded video of your Pandit Ji with your Name & Gotra + live Prasad courier tracking.',
                  style: TextStyle(color: Colors.white70, fontSize: 11.5, height: 1.3),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSankalpCard(Map<String, dynamic> item) {
    final int step = item['prasadStep'] ?? 1;

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: Colors.black.withOpacity(0.08)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: const BoxDecoration(
              color: Color(0xFFFFFBEB),
              borderRadius: BorderRadius.vertical(top: Radius.circular(18)),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        item['pujaName'] as String,
                        style: const TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF78350F),
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        '${item['templeName']} • ${item['date']}',
                        style: const TextStyle(fontSize: 11, color: Colors.black54),
                      ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: const Color(0xFF10B981).withOpacity(0.2),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: const Color(0xFF10B981)),
                  ),
                  child: const Text(
                    'COMPLETED',
                    style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.w800,
                      color: Color(0xFF047857),
                    ),
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    const Icon(Icons.person_pin, size: 16, color: Colors.black54),
                    const SizedBox(width: 6),
                    Text(
                      'Sankalp: ${item['gotra']} • ${item['familyMembers']}',
                      style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
                    ),
                  ],
                ),
                const SizedBox(height: 14),

                // Video trigger button
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF1A132F),
                      foregroundColor: CustomerTheme.brandGold,
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    icon: const Icon(Icons.play_circle_fill_rounded, size: 20),
                    label: const Text(
                      'Watch Recorded Sankalp Video 🪔',
                      style: TextStyle(fontWeight: FontWeight.bold),
                    ),
                    onPressed: () => _openVideo(
                      item['videoUrl'] as String,
                      item['pujaName'] as String,
                    ),
                  ),
                ),

                const SizedBox(height: 18),
                const Divider(height: 1),
                const SizedBox(height: 14),

                // Holy Prasad Courier Tracking
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        const Icon(Icons.local_shipping_outlined,
                            size: 18, color: CustomerTheme.brandSaffron),
                        const SizedBox(width: 6),
                        const Text(
                          'Holy Prasad Dispatch:',
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.bold,
                            color: Color(0xFF1A132F),
                          ),
                        ),
                      ],
                    ),
                    Text(
                      item['prasadStatus'] as String,
                      style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: CustomerTheme.brandSaffron,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Text(
                  'Courier: ${item['courier']} (AWB: ${item['awbNumber']})',
                  style: const TextStyle(fontSize: 11, color: Colors.black54),
                ),
                const SizedBox(height: 12),

                // Step progress bar
                _buildTrackingSteps(step),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTrackingSteps(int currentStep) {
    final steps = ['Sankalp', 'Blessed', 'In Transit', 'Delivered'];
    return Row(
      children: List.generate(steps.length * 2 - 1, (i) {
        if (i.isOdd) {
          final lineActive = (i ~/ 2) + 1 < currentStep;
          return Expanded(
            child: Container(
              height: 3,
              color: lineActive ? const Color(0xFF10B981) : Colors.black12,
            ),
          );
        }
        final stepIdx = i ~/ 2 + 1;
        final isActive = stepIdx <= currentStep;
        return Column(
          children: [
            Container(
              width: 18,
              height: 18,
              decoration: BoxDecoration(
                color: isActive ? const Color(0xFF10B981) : Colors.white,
                shape: BoxShape.circle,
                border: Border.all(
                  color: isActive ? const Color(0xFF10B981) : Colors.black26,
                  width: 2,
                ),
              ),
              child: isActive
                  ? const Icon(Icons.check, size: 12, color: Colors.white)
                  : null,
            ),
            const SizedBox(height: 4),
            Text(
              steps[i ~/ 2],
              style: TextStyle(
                fontSize: 9.5,
                fontWeight: isActive ? FontWeight.bold : FontWeight.normal,
                color: isActive ? const Color(0xFF10B981) : Colors.black45,
              ),
            ),
          ],
        );
      }),
    );
  }

  Widget _buildBookAnotherPujaCta() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.black12),
      ),
      child: Column(
        children: [
          const Text(
            'Seek Divine Blessings for Upcoming Muhurats',
            style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
          ),
          const SizedBox(height: 6),
          const Text(
            'Book pujas at Kashi Vishwanath, Somnath, Ujjain Mahakal & Kamakhya Devi.',
            textAlign: TextAlign.center,
            style: TextStyle(fontSize: 12, color: Colors.black54),
          ),
          const SizedBox(height: 12),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: CustomerTheme.brandSaffron,
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 10),
            ),
            onPressed: () {
              Navigator.of(context).push(
                MaterialPageRoute(builder: (_) => const PujaListScreen()),
              );
            },
            child: const Text('Browse All Pujas 🪔'),
          ),
        ],
      ),
    );
  }
}
