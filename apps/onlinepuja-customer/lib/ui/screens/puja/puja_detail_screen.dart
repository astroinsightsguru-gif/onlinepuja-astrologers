import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import '../checkout/checkout_screen.dart';

/// Sri Mandir-Grade High-Conversion Puja & Chadhava Detail Experience
class PujaDetailScreen extends StatefulWidget {
  const PujaDetailScreen({super.key, required this.puja});

  final Puja puja;

  @override
  State<PujaDetailScreen> createState() => _PujaDetailScreenState();
}

class _PujaDetailScreenState extends State<PujaDetailScreen> {
  List<Map<String, dynamic>>? _faqs;
  PujaPackage? _selectedPackage;
  int _selectedPackageIndex = 0;

  @override
  void initState() {
    super.initState();
    final pkgs = widget.puja.packages ?? const <PujaPackage>[];
    if (pkgs.isNotEmpty) {
      _selectedPackage = pkgs.first;
    }
    _loadFaqs();
  }

  Future<void> _loadFaqs() async {
    try {
      final faqs = await PujaApi.instance.faqs(pujaId: widget.puja.id);
      if (mounted) setState(() => _faqs = faqs);
    } catch (_) {
      if (mounted) setState(() => _faqs = const []);
    }
  }

  static String _formatDate(dynamic v) {
    final s = v?.toString() ?? '';
    return s.length >= 10 ? s.substring(0, 10) : s;
  }

  void _proceedToBooking() {
    final packages = widget.puja.packages ?? const <PujaPackage>[];
    PujaPackage? pkg = _selectedPackage ?? (packages.isNotEmpty ? packages.first : null);
    pkg ??= PujaPackage(
      id: 1,
      name: 'Vedic Sankalp Seva',
      price: '501',
      inclusions: const [
        'Personal Sankalp with Name & Gotra',
        'Vedic Mantra chanting by Acharyas',
        'Puja video recorded & sent on WhatsApp',
        'Holy Prasad delivered to your address',
      ],
    );

    Navigator.of(context).push(MaterialPageRoute(
      builder: (_) => CheckoutScreen.puja(
        puja: widget.puja,
        package: pkg!,
      ),
    ));
  }

  @override
  Widget build(BuildContext context) {
    final p = widget.puja;
    final packages = (p.packages != null && p.packages!.isNotEmpty)
        ? p.packages!
        : [
            PujaPackage(
              id: 1,
              name: 'Individual Sankalp',
              price: '501',
              inclusions: const [
                'Sankalp for 1 devotee with Name & Gotra',
                'Live Chadhava & Pushpanjali offered',
                'Personalized Video sent on WhatsApp',
              ],
            ),
            PujaPackage(
              id: 2,
              name: 'Family Maha Sankalp',
              price: '1100',
              inclusions: const [
                'Sankalp for up to 4 family members',
                'Special Archana & Navgrah Ahuti',
                'Puja Video + Prasad delivered to home',
              ],
            ),
            PujaPackage(
              id: 3,
              name: 'Sampoorna Teerth Mahapuja',
              price: '2100',
              inclusions: const [
                'Complete Vedic Hawan & Brahmin Bhojan',
                'All family members included in Sankalp',
                'Special Energized Raksha Sutra & Mahaprasad box',
              ],
            ),
          ];

    if (_selectedPackage == null && packages.isNotEmpty) {
      _selectedPackage = packages.first;
    }

    final currentPrice = _selectedPackage?.priceValue ?? 501.0;

    return Scaffold(
      backgroundColor: const Color(0xFFF9F7F2),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0.5,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              p.title?.toString() ?? 'Puja Details',
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w800,
                color: Color(0xFF1E293B),
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
            const Text(
              'Verified Vedic Temple Ritual',
              style: TextStyle(fontSize: 11, color: Color(0xFF64748B)),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.share_outlined, color: Color(0xFF475569)),
            onPressed: () {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Puja link copied to share')),
              );
            },
          ),
        ],
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.08),
              blurRadius: 10,
              offset: const Offset(0, -3),
            ),
          ],
        ),
        child: SafeArea(
          child: Row(
            children: [
              Expanded(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      _selectedPackage?.name ?? 'Selected Package',
                      style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: Color(0xFF64748B),
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    Row(
                      children: [
                        Text(
                          '₹${currentPrice.toStringAsFixed(0)}',
                          style: const TextStyle(
                            fontSize: 22,
                            fontWeight: FontWeight.w900,
                            color: Color(0xFF1E293B),
                          ),
                        ),
                        const SizedBox(width: 6),
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 6, vertical: 2),
                          decoration: BoxDecoration(
                            color: const Color(0xFFDCFCE7),
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: const Text(
                            'All-Inclusive',
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.w800,
                              color: Color(0xFF15803D),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 14),
              ElevatedButton(
                onPressed: _proceedToBooking,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF059669),
                  foregroundColor: Colors.white,
                  padding:
                      const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                  elevation: 2,
                ),
                child: const Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      'PARTICIPATE',
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w800,
                        letterSpacing: 0.5,
                      ),
                    ),
                    SizedBox(width: 6),
                    Icon(Icons.arrow_forward_rounded, size: 18),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
      body: ListView(
        padding: EdgeInsets.zero,
        children: [
          // 1. Hero Image with Badges
          Stack(
            children: [
              AspectRatio(
                aspectRatio: 16 / 10,
                child: p.coverImage.isNotEmpty
                    ? CachedNetworkImage(
                        imageUrl: MiscApi.imageUrl(p.coverImage),
                        fit: BoxFit.cover,
                        errorWidget: (_, _, _) => Container(
                          color: const Color(0xFFFEF3C7),
                          child: const Icon(Icons.temple_hindu_rounded,
                              size: 64, color: Color(0xFFD97706)),
                        ),
                      )
                    : Container(
                        color: const Color(0xFFFEF3C7),
                        child: const Icon(Icons.temple_hindu_rounded,
                            size: 64, color: Color(0xFFD97706)),
                      ),
              ),
              Positioned.fill(
                child: DecoratedBox(
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      colors: [
                        Colors.black.withOpacity(0.6),
                        Colors.transparent,
                        Colors.black.withOpacity(0.8),
                      ],
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                      stops: const [0.0, 0.4, 1.0],
                    ),
                  ),
                ),
              ),
              // Top Holy Teerth Badge
              Positioned(
                top: 14,
                left: 14,
                child: Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                  decoration: BoxDecoration(
                    color: Colors.black.withOpacity(0.7),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(
                        color: const Color(0xFFF59E0B).withOpacity(0.6)),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.verified_rounded,
                          size: 14, color: Color(0xFFF59E0B)),
                      const SizedBox(width: 5),
                      Text(
                        (p.place ?? '').toString().isNotEmpty
                            ? p.place.toString()
                            : 'Holy Teerth Kshetra',
                        style: const TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                          color: Colors.white,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              // Bottom Devotees Sankalp Badge
              Positioned(
                bottom: 14,
                left: 14,
                right: 14,
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: const Color(0xFF059669),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: const Row(
                        children: [
                          Icon(Icons.people_alt_rounded,
                              size: 13, color: Colors.white),
                          SizedBox(width: 4),
                          Text(
                            '14,250+ Devotees Participated',
                            style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w800,
                              color: Colors.white,
                            ),
                          ),
                        ],
                      ),
                    ),
                    if (_formatDate(p.startDatetime).isNotEmpty)
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.black.withOpacity(0.7),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          '📅 ${_formatDate(p.startDatetime)}',
                          style: const TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w700,
                            color: Color(0xFFFDE68A),
                          ),
                        ),
                      ),
                  ],
                ),
              ),
            ],
          ),

          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Title and Subtitle
                Text(
                  p.title?.toString() ?? 'Vedic Puja',
                  style: const TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.w900,
                    color: Color(0xFF1E293B),
                    height: 1.2,
                  ),
                ),
                if ((p.subtitle ?? '').toString().isNotEmpty) ...[
                  const SizedBox(height: 6),
                  Text(
                    p.subtitle.toString(),
                    style: const TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w500,
                      color: Color(0xFF64748B),
                    ),
                  ),
                ],

                const SizedBox(height: 16),

                // 2. Trust Strip (WhatsApp Video + Prasad + Verified Acharyas)
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: const Color(0xFFE2E8F0)),
                  ),
                  child: Row(
                    children: [
                      _trustItem(
                        icon: Icons.video_camera_front_rounded,
                        color: const Color(0xFF25D366),
                        title: 'Video Proof',
                        subtitle: 'On WhatsApp',
                      ),
                      const SizedBox(
                        height: 28,
                        child: VerticalDivider(
                            color: Color(0xFFE2E8F0), thickness: 1),
                      ),
                      _trustItem(
                        icon: Icons.local_shipping_rounded,
                        color: const Color(0xFFF59E0B),
                        title: 'Prasad Box',
                        subtitle: 'SpeedPost Delivery',
                      ),
                      const SizedBox(
                        height: 28,
                        child: VerticalDivider(
                            color: Color(0xFFE2E8F0), thickness: 1),
                      ),
                      _trustItem(
                        icon: Icons.workspace_premium_rounded,
                        color: const Color(0xFF3B82F6),
                        title: 'Kashi Acharyas',
                        subtitle: '100% Vedic Mantras',
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 20),

                // 3. Select Package (Sri Mandir interactive cards)
                const Row(
                  children: [
                    Icon(Icons.card_giftcard_rounded,
                        size: 20, color: Color(0xFFD97706)),
                    SizedBox(width: 8),
                    Text(
                      'Choose Your Puja Package',
                      style: TextStyle(
                        fontSize: 17,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF1E293B),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 4),
                const Text(
                  'Select the seva tier for your family’s sankalp',
                  style: TextStyle(fontSize: 12, color: Color(0xFF64748B)),
                ),
                const SizedBox(height: 12),

                // Package List
                for (int i = 0; i < packages.length; i++) ...[
                  _packageCard(packages[i], i),
                  const SizedBox(height: 10),
                ],

                const SizedBox(height: 20),

                // 4. About the Puja
                if ((p.longDescription ?? '').toString().isNotEmpty) ...[
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: const Color(0xFFE2E8F0)),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Row(
                          children: [
                            Icon(Icons.auto_stories_rounded,
                                size: 18, color: Color(0xFFD97706)),
                            SizedBox(width: 8),
                            Text(
                              'Significance & Vidhi',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w800,
                                color: Color(0xFF1E293B),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 10),
                        Text(
                          p.longDescription.toString(),
                          style: const TextStyle(
                            fontSize: 13.5,
                            color: Color(0xFF475569),
                            height: 1.5,
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),
                ],

                // 5. Puja Benefits
                if ((p.benefits ?? const <String>[]).isNotEmpty) ...[
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: const Color(0xFFFFFBEB),
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: const Color(0xFFFDE68A)),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Row(
                          children: [
                            Icon(Icons.stars_rounded,
                                size: 18, color: Color(0xFFD97706)),
                            SizedBox(width: 8),
                            Text(
                              'Divine Blessings & Benefits',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w800,
                                color: Color(0xFF92400E),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        for (final b in p.benefits!) ...[
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Padding(
                                padding: EdgeInsets.only(top: 2),
                                child: Icon(Icons.check_circle_rounded,
                                    size: 16, color: Color(0xFF059669)),
                              ),
                              const SizedBox(width: 8),
                              Expanded(
                                child: Text(
                                  b,
                                  style: const TextStyle(
                                    fontSize: 13,
                                    color: Color(0xFF78350F),
                                    fontWeight: FontWeight.w600,
                                    height: 1.4,
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 8),
                        ],
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),
                ],

                // 6. 4-Step Process Guide
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: const Color(0xFFE2E8F0)),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'How Online Puja Works',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.w800,
                          color: Color(0xFF1E293B),
                        ),
                      ),
                      const SizedBox(height: 14),
                      _stepRow(
                        num: '1',
                        title: 'Select Seva & Fill Devotee Details',
                        desc: 'Provide your Gotra, Name & Sankalp Wish at checkout.',
                      ),
                      _stepRow(
                        num: '2',
                        title: 'Pandit Ji performs Sankalp at Teerth',
                        desc: 'Vedic rituals conducted strictly per your astrological timings.',
                      ),
                      _stepRow(
                        num: '3',
                        title: 'Video Proof Shared on WhatsApp',
                        desc: 'Receive recorded personal video & chadhava darshan photos.',
                      ),
                      _stepRow(
                        num: '4',
                        title: 'Holy Prasad Delivered to Home',
                        desc: 'Energized Raksha Sutra, Vibhuti & Prasad shipped in 3-5 days.',
                        isLast: true,
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 20),

                // 7. FAQs
                if (_faqs != null && _faqs!.isNotEmpty) ...[
                  const Text(
                    'Frequently Asked Questions',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w800,
                      color: Color(0xFF1E293B),
                    ),
                  ),
                  const SizedBox(height: 8),
                  for (final f in _faqs!)
                    Container(
                      margin: const EdgeInsets.only(bottom: 8),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                      ),
                      child: ExpansionTile(
                        tilePadding:
                            const EdgeInsets.symmetric(horizontal: 14),
                        title: Text(
                          (f['question'] ?? f['title'] ?? 'Q').toString(),
                          style: const TextStyle(
                            fontSize: 13.5,
                            fontWeight: FontWeight.w700,
                            color: Color(0xFF1E293B),
                          ),
                        ),
                        childrenPadding: const EdgeInsets.only(
                            left: 14, right: 14, bottom: 12),
                        children: [
                          Align(
                            alignment: Alignment.centerLeft,
                            child: Text(
                              (f['answer'] ?? f['description'] ?? '')
                                  .toString(),
                              style: const TextStyle(
                                fontSize: 13,
                                color: Color(0xFF64748B),
                                height: 1.4,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                ],

                const SizedBox(height: 30),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _trustItem({
    required IconData icon,
    required Color color,
    required String title,
    required String subtitle,
  }) {
    return Expanded(
      child: Column(
        children: [
          Icon(icon, size: 22, color: color),
          const SizedBox(height: 4),
          Text(
            title,
            style: const TextStyle(
              fontSize: 11.5,
              fontWeight: FontWeight.w800,
              color: Color(0xFF1E293B),
            ),
            textAlign: TextAlign.center,
          ),
          Text(
            subtitle,
            style: const TextStyle(
              fontSize: 9.5,
              fontWeight: FontWeight.w500,
              color: Color(0xFF64748B),
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _packageCard(PujaPackage pkg, int index) {
    final isSelected = _selectedPackageIndex == index;
    final isPopular = index == 1;

    return GestureDetector(
      onTap: () {
        setState(() {
          _selectedPackageIndex = index;
          _selectedPackage = pkg;
        });
      },
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
            color: isSelected
                ? const Color(0xFF059669)
                : const Color(0xFFE2E8F0),
            width: isSelected ? 2 : 1,
          ),
          boxShadow: [
            if (isSelected)
              BoxShadow(
                color: const Color(0xFF059669).withOpacity(0.12),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(
                  isSelected
                      ? Icons.radio_button_checked_rounded
                      : Icons.radio_button_off_rounded,
                  color: isSelected
                      ? const Color(0xFF059669)
                      : const Color(0xFF94A3B8),
                  size: 20,
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    pkg.name?.toString() ?? 'Package',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w800,
                      color: isSelected
                          ? const Color(0xFF059669)
                          : const Color(0xFF1E293B),
                    ),
                  ),
                ),
                if (isPopular)
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 8, vertical: 2),
                    margin: const EdgeInsets.only(right: 8),
                    decoration: BoxDecoration(
                      color: const Color(0xFFFEF3C7),
                      borderRadius: BorderRadius.circular(6),
                      border: Border.all(color: const Color(0xFFF59E0B)),
                    ),
                    child: const Text(
                      'MOST POPULAR',
                      style: TextStyle(
                        fontSize: 9.5,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFFB45309),
                      ),
                    ),
                  ),
                Text(
                  '₹${pkg.priceValue.toStringAsFixed(0)}',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w900,
                    color: isSelected
                        ? const Color(0xFF059669)
                        : const Color(0xFF1E293B),
                  ),
                ),
              ],
            ),
            if (pkg.inclusions != null && pkg.inclusions!.isNotEmpty) ...[
              const Divider(height: 18, color: Color(0xFFF1F5F9)),
              for (final inc in pkg.inclusions!) ...[
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Padding(
                      padding: EdgeInsets.only(top: 2),
                      child: Icon(Icons.check_circle_outline_rounded,
                          size: 14, color: Color(0xFF059669)),
                    ),
                    const SizedBox(width: 6),
                    Expanded(
                      child: Text(
                        inc,
                        style: const TextStyle(
                          fontSize: 12,
                          color: Color(0xFF475569),
                          height: 1.3,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 4),
              ],
            ],
          ],
        ),
      ),
    );
  }

  Widget _stepRow({
    required String num,
    required String title,
    required String desc,
    bool isLast = false,
  }) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Column(
          children: [
            Container(
              width: 24,
              height: 24,
              alignment: Alignment.center,
              decoration: const BoxDecoration(
                color: Color(0xFFFEF3C7),
                shape: BoxShape.circle,
              ),
              child: Text(
                num,
                style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w800,
                  color: Color(0xFFB45309),
                ),
              ),
            ),
            if (!isLast)
              Container(
                width: 1.5,
                height: 36,
                color: const Color(0xFFE2E8F0),
              ),
          ],
        ),
        const SizedBox(width: 10),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: const TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w700,
                  color: Color(0xFF1E293B),
                ),
              ),
              const SizedBox(height: 2),
              Text(
                desc,
                style: const TextStyle(
                  fontSize: 11.5,
                  color: Color(0xFF64748B),
                  height: 1.3,
                ),
              ),
              const SizedBox(height: 12),
            ],
          ),
        ),
      ],
    );
  }
}
