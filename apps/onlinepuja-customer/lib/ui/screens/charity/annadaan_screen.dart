import 'package:provider/provider.dart';
import '../../../state/app_session.dart';
import '../profile/wallet_screen.dart';
import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

/// Represents a holy charitable cause.
class SevaCause {
  final String id;
  final String title;
  final String location;
  final String icon;
  final int amount;
  final String description;
  final String impact;

  const SevaCause({
    required this.id,
    required this.title,
    required this.location,
    required this.icon,
    required this.amount,
    required this.description,
    required this.impact,
  });
}

/// Digital Annadaan, Gau Seva & Dharmic Charity screen.
class AnnadaanScreen extends StatefulWidget {
  const AnnadaanScreen({super.key});

  static const route = '/annadaan';

  @override
  State<AnnadaanScreen> createState() => _AnnadaanScreenState();
}

class _AnnadaanScreenState extends State<AnnadaanScreen> {
  final _nameController = TextEditingController();
  final _gotraController = TextEditingController();
  late SevaCause _selectedCause;

  static const List<SevaCause> _causes = [
    SevaCause(
      id: 'gau_seva',
      title: 'Gau Seva & Fodder Offering',
      location: 'Vrindavan Gaushala, Mathura',
      icon: '🐄',
      amount: 51,
      description: 'Sponsor green grass, fresh jaggery, and nutrition for sacred cows at ancient Vrindavan Gaushalas.',
      impact: '1 Cow Fed for a Full Day with your family Sankalp.',
    ),
    SevaCause(
      id: 'sadhu_bhojan',
      title: 'Sadhu & Sant Satvik Bhojan',
      location: 'Assi Ghat, Varanasi',
      icon: '🍲',
      amount: 101,
      description: 'Offer wholesome, hot Satvik Mahaprasad (Dal, Rice, Roti, Kheer) to meditating sadhus and sants.',
      impact: 'Feeds 2 Sadhus in the holy city of Kashi.',
    ),
    SevaCause(
      id: 'akhand_jyot',
      title: 'Akhand Jyot & Temple Lamp Seva',
      location: 'Kashi Vishwanath Corridor, Varanasi',
      icon: '🪔',
      amount: 151,
      description: 'Sponsor pure sesame oil and ghee for the eternal Akhand Jyot burning continuously at the Jyotirlinga.',
      impact: 'Continuous prayer light lit with your Name & Gotra.',
    ),
    SevaCause(
      id: 'gurukul_vidya',
      title: 'Vedic Gurukul Vidyadaan',
      location: 'Rishikesh Ashram Gurukul',
      icon: '📚',
      amount: 251,
      description: 'Support young brahmacharis and vedic students studying the Vedas, Upanishads, and Sanskrit grammar.',
      impact: 'Provides monthly study material for 1 student.',
    ),
  ];

  @override
  void initState() {
    super.initState();
    _selectedCause = _causes.first;
  }

  @override
  void dispose() {
    _nameController.dispose();
    _gotraController.dispose();
    super.dispose();
  }

    Future<void> _submitSeva() async {
    final name = _nameController.text.trim();
    if (name.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please enter the devotee name for Sankalp.')),
      );
      return;
    }

    final session = context.read<AppSession>();
    if (!session.isAuthenticated || session.user == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please log in to offer Seva.')),
      );
      return;
    }

    final wallet = session.user?.walletAmount ?? 0.0;
    if (wallet < _selectedCause.amount) {
      final recharge = await showDialog<bool>(
        context: context,
        builder: (ctx) => AlertDialog(
          title: const Text('Insufficient Wallet Balance'),
          content: Text('Your wallet balance is ₹${wallet.toStringAsFixed(1)}. You need ₹${_selectedCause.amount} for this sacred offering.'),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancel')),
            FilledButton(
              onPressed: () => Navigator.pop(ctx, true),
              child: const Text('Recharge Wallet'),
            ),
          ],
        ),
      );
      if (recharge == true && mounted) {
        Navigator.of(context).pushNamed(WalletScreen.route).then((_) => session.refreshUser());
      }
      return;
    }

    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('Confirm ${_selectedCause.title}'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Devotee: $name'),
            Text('Gotra: ${_gotraController.text.isEmpty ? "Kashyap (Default)" : _gotraController.text}'),
            const SizedBox(height: 8),
            Text('Offering Amount: ₹${_selectedCause.amount}', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
            const SizedBox(height: 8),
            const Text(
              'A video clip and holy blessings certificate will be sent to your orders section within 24 hours.',
              style: TextStyle(fontSize: 12, color: Colors.black54),
            ),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancel')),
          FilledButton(
            style: FilledButton.styleFrom(backgroundColor: AppTheme.brandSaffron),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Confirm & Pay'),
          ),
        ],
      ),
    );

    if (confirm != true || !mounted) return;

    try {
      final res = await ApiClient.instance.post('/annadaan/donate', body: {
        'userId': session.user?.id,
        'causeTitle': _selectedCause.title,
        'devoteeName': name,
        'gotra': _gotraController.text.trim().isEmpty ? 'Kashyap' : _gotraController.text.trim(),
        'amount': _selectedCause.amount,
      });

      await session.refreshUser();

      if (!mounted) return;
      final orderId = (res is Map && res['orderId'] != null) ? res['orderId'] : 'OP-SEVA';
      final redirectUrl = (res is Map && res['redirect'] != null) ? res['redirect'].toString() : null;

      if (redirectUrl != null && redirectUrl.isNotEmpty) {
        final uri = Uri.parse(redirectUrl);
        await launchUrl(uri, mode: LaunchMode.externalApplication);
      }

      if (!mounted) return;
      showDialog(
        context: context,
        builder: (ctx) => AlertDialog(
          icon: const Icon(Icons.volunteer_activism_rounded, color: Colors.amber, size: 48),
          title: const Text('Seva Offering Sanctified 🙏'),
          content: Text('Your offering for "${_selectedCause.title}" has been registered successfully.\n\nOrder ID: $orderId\n\nMay the Almighty shower divine blessings upon $name and your family.'),
          actions: [
            FilledButton(
              onPressed: () => Navigator.pop(ctx),
              child: const Text('Har Har Mahadev'),
            ),
          ],
        ),
      );
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Offering failed: $e'), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Annadaan & Holy Seva'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Banner
            Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [AppTheme.brandDeep, AppTheme.brandDeep.withValues(alpha: 0.85)],
                ),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: AppTheme.brandSaffron.withValues(alpha: 0.4)),
              ),
              child: Row(
                children: [
                  Container(
                    width: 52,
                    height: 52,
                    decoration: BoxDecoration(
                      color: AppTheme.brandSaffron.withValues(alpha: 0.2),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: const Center(child: Text('🌾', style: TextStyle(fontSize: 26))),
                  ),
                  const SizedBox(width: 14),
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Direct Dham Seva', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)),
                        SizedBox(height: 3),
                        Text(
                          'Support sacred charity with full transparency. Receive verified photos and video proof.',
                          style: TextStyle(color: Colors.white70, fontSize: 12),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 22),

            Text('Select Holy Seva Cause:', style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),

            // Cause Selector Cards
            ..._causes.map((c) {
              final isSelected = c.id == _selectedCause.id;

              return InkWell(
                borderRadius: BorderRadius.circular(16),
                onTap: () => setState(() => _selectedCause = c),
                child: Container(
                  margin: const EdgeInsets.only(bottom: 12),
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: isSelected
                        ? AppTheme.brandSaffron.withValues(alpha: 0.12)
                        : scheme.surface,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: isSelected ? AppTheme.brandSaffron : scheme.outline.withValues(alpha: 0.25),
                      width: isSelected ? 2 : 1,
                    ),
                  ),
                  child: Row(
                    children: [
                      Text(c.icon, style: const TextStyle(fontSize: 32)),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Expanded(
                                  child: Text(
                                    c.title,
                                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                                  ),
                                ),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                  decoration: BoxDecoration(
                                    color: AppTheme.brandDeep,
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: Text(
                                    '₹${c.amount}',
                                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 2),
                            Text(c.location, style: TextStyle(color: scheme.outline, fontSize: 11)),
                            const SizedBox(height: 6),
                            Text(c.impact, style: const TextStyle(color: Colors.green, fontSize: 11.5, fontWeight: FontWeight.bold)),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              );
            }),

            const SizedBox(height: 16),
            Text('Sankalp Details:', style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),

            TextField(
              controller: _nameController,
              decoration: InputDecoration(
                labelText: 'Devotee / Family Name',
                hintText: 'e.g. Ramesh Sharma & Family',
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              ),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _gotraController,
              decoration: InputDecoration(
                labelText: 'Gotra (Optional)',
                hintText: 'e.g. Kashyap, Bhardwaj',
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              ),
            ),
            const SizedBox(height: 20),

            SizedBox(
              width: double.infinity,
              height: 50,
              child: FilledButton(
                style: FilledButton.styleFrom(
                  backgroundColor: AppTheme.brandSaffron,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                ),
                onPressed: _submitSeva,
                child: Text('Sponsor ${_selectedCause.title} · ₹${_selectedCause.amount}',
                    style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
