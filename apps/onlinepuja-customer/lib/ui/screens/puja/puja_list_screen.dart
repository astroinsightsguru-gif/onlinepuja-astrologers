import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import 'puja_detail_screen.dart';
import '../history/history_screen.dart';

/// Sri Mandir-Grade Puja & Chadhava Experience
class PujaListScreen extends StatefulWidget {
  const PujaListScreen({super.key});

  static const route = '/pujas';

  @override
  State<PujaListScreen> createState() => _PujaListScreenState();
}

class _PujaListScreenState extends State<PujaListScreen> {
  List<PujaCategory>? _categories;
  List<Puja>? _items;
  Object? _error;
  dynamic _categoryId;
  String _selectedDeity = "All";
  bool _isPlaying = true;

  final List<String> _deities = [
    "All",
    "Durga",
    "Shiva",
    "Ganesh",
    "Lakshmi",
    "Vishnu",
    "Shradh Special",
  ];

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _error = null);
    try {
      final cats = await PujaApi.instance.categories();
      final items = await PujaApi.instance.list(categoryId: _categoryId);
      if (mounted) {
        setState(() {
          _categories = cats;
          _items = items;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _error = e);
    }
  }

  void _pickCategory(dynamic id) {
    setState(() => _categoryId = (_categoryId == id) ? null : id);
    _load();
  }

  static String _formatTithi(dynamic rawDate) {
    if (rawDate == null) return "Auspicious Vedic Tithi";
    final s = rawDate.toString();
    if (s.length >= 10) {
      return "${s.substring(0, 10)} • Sacred Tithi";
    }
    return s;
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;

    return Scaffold(
      backgroundColor: const Color(0xFFF9F7F2),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0.5,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: const [
            Text(
              'Puja & Chadhava Seva',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w800,
                color: Color(0xFF1E293B),
              ),
            ),
            Text(
              'Vedic Sankalp at Holy Teerths',
              style: TextStyle(fontSize: 11, color: Color(0xFF64748B)),
            ),
          ],
        ),
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: 12),
            child: InkWell(
              onTap: () {
                Navigator.of(context).push(
                  MaterialPageRoute(builder: (_) => const HistoryScreen()),
                );
              },
              borderRadius: BorderRadius.circular(16),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                decoration: BoxDecoration(
                  color: const Color(0xFFFEF3C7),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: const Color(0xFFFBBF24)),
                ),
                child: Row(
                  children: const [
                    Text("🛍️ ", style: TextStyle(fontSize: 12)),
                    Text(
                      "Bookings",
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w800,
                        color: Color(0xFF92400E),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
      body: _error != null
          ? StatusViews.error(context, _error!, onRetry: _load)
          : Stack(
        children: [
          RefreshIndicator(
            onRefresh: _load,
            child: CustomScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              slivers: [
                // 1. Festive Hero Mahotsav Banner
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(14, 12, 14, 8),
                    child: Container(
                      height: 145,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(18),
                        gradient: const LinearGradient(
                          colors: [Color(0xFF78350F), Color(0xFF92400E), Color(0xFFB45309)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        boxShadow: [
                          BoxShadow(
                            color: const Color(0xFF78350F).withValues(alpha: 0.3),
                            blurRadius: 10,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Stack(
                        children: [
                          Positioned(
                            right: -10,
                            bottom: -15,
                            child: Opacity(
                              opacity: 0.12,
                              child: const Text("ॐ", style: TextStyle(fontSize: 140, color: Colors.white)),
                            ),
                          ),
                          Padding(
                            padding: const EdgeInsets.all(16.0),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFFBBF24),
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                  child: const Text(
                                    "VEDIC SANKALPA & SEVA",
                                    style: TextStyle(
                                      fontSize: 9,
                                      fontWeight: FontWeight.w900,
                                      color: Color(0xFF78350F),
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                ),
                                const SizedBox(height: 6),
                                const Text(
                                  "Navratri & Shradh Mahotsav",
                                  style: TextStyle(
                                    color: Colors.white,
                                    fontSize: 16.5,
                                    fontWeight: FontWeight.w800,
                                    height: 1.2,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                const Text(
                                  "Participate in Sacred Pujas at Kashi, Gaya & Holy Teerths",
                                  style: TextStyle(color: Colors.white70, fontSize: 11),
                                ),
                                const SizedBox(height: 8),
                                Row(
                                  children: const [
                                    Text(
                                      "Explore Sevas ➔",
                                      style: TextStyle(
                                        color: Color(0xFFFDE68A),
                                        fontSize: 11,
                                        fontWeight: FontWeight.w700,
                                      ),
                                    ),
                                  ],
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),

                // 2. Circular Deity Filter Chips
                SliverToBoxAdapter(
                  child: SizedBox(
                    height: 48,
                    child: ListView.builder(
                      scrollDirection: Axis.horizontal,
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      itemCount: _deities.length,
                      itemBuilder: (context, index) {
                        final deity = _deities[index];
                        final isSelected = _selectedDeity == deity;
                        return Padding(
                          padding: const EdgeInsets.only(right: 6),
                          child: ChoiceChip(
                            label: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                if (deity == "All") ...[
                                  const Text("ॐ ", style: TextStyle(color: Color(0xFFD97706), fontWeight: FontWeight.bold)),
                                ],
                                Text(deity, style: TextStyle(fontSize: 11, fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500)),
                              ],
                            ),
                            selected: isSelected,
                            selectedColor: const Color(0xFFD97706),
                            backgroundColor: Colors.white,
                            labelStyle: TextStyle(color: isSelected ? Colors.white : Colors.black87),
                            onSelected: (val) {
                              setState(() => _selectedDeity = deity);
                            },
                          ),
                        );
                      },
                    ),
                  ),
                ),

                // 3. Category Horizontal List
                if (_categories != null && _categories!.isNotEmpty)
                  SliverToBoxAdapter(
                    child: SizedBox(
                      height: 40,
                      child: ListView.separated(
                        scrollDirection: Axis.horizontal,
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
                        itemCount: _categories!.length,
                        separatorBuilder: (_, _) => const SizedBox(width: 8),
                        itemBuilder: (context, i) {
                          final c = _categories![i];
                          return FilterChip(
                            label: Text(c.name?.toString() ?? '', style: const TextStyle(fontSize: 11)),
                            selected: _categoryId == c.id,
                            selectedColor: const Color(0xFFFEF3C7),
                            checkmarkColor: const Color(0xFFD97706),
                            onSelected: (_) => _pickCategory(c.id),
                          );
                        },
                      ),
                    ),
                  ),

                // 4. Sacred Puja Cards Feed
                if (_items == null)
                  const SliverPadding(
                    padding: EdgeInsets.all(60),
                    sliver: SliverToBoxAdapter(
                      child: Center(child: CircularProgressIndicator(strokeWidth: 2.4, color: Color(0xFFD97706))),
                    ),
                  )
                else if (_items!.isEmpty)
                  SliverToBoxAdapter(
                    child: SizedBox(
                      height: 300,
                      child: StatusViews.empty(context, message: 'No pujas found in this category'),
                    ),
                  )
                else
                  SliverPadding(
                    padding: const EdgeInsets.fromLTRB(14, 10, 14, 90),
                    sliver: SliverList.separated(
                      itemCount: _items!.length,
                      separatorBuilder: (_, _) => const SizedBox(height: 16),
                      itemBuilder: (context, i) => _buildSriMandirPujaCard(context, _items![i], scheme),
                    ),
                  ),
              ],
            ),
          ),

          // 5. Docked Devotional Mini Audio Player
          Positioned(
            left: 12,
            right: 12,
            bottom: 12,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
              decoration: BoxDecoration(
                color: const Color(0xFF1E293B),
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.25),
                    blurRadius: 12,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Row(
                children: [
                  Container(
                    width: 36,
                    height: 36,
                    decoration: const BoxDecoration(
                      shape: BoxShape.circle,
                      color: Color(0xFFD97706),
                    ),
                    child: const Center(
                      child: Text("🕉️", style: TextStyle(fontSize: 18)),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisSize: MainAxisSize.min,
                      children: const [
                        Text(
                          "Vakratunda Mahakaya • Dhyaan",
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 11.5,
                            fontWeight: FontWeight.w700,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        Text(
                          "Sri Mandir Devotional Chants",
                          style: TextStyle(color: Colors.white60, fontSize: 9.5),
                        ),
                      ],
                    ),
                  ),
                  IconButton(
                    icon: Icon(
                      _isPlaying ? Icons.pause_circle_filled : Icons.play_circle_filled,
                      color: const Color(0xFFFBBF24),
                      size: 28,
                    ),
                    onPressed: () {
                      setState(() => _isPlaying = !_isPlaying);
                    },
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSriMandirPujaCard(BuildContext context, Puja p, ColorScheme scheme) {
    final price = p.startingPrice ?? 501;
    final place = p.place?.toString() ?? "Holy Pilgrimage Teerth";
    final tithi = _formatTithi(p.startDatetime);

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () => Navigator.of(context).push(
          MaterialPageRoute(builder: (_) => PujaDetailScreen(puja: p)),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Top Image Banner with Badges
            Stack(
              children: [
                SizedBox(
                  height: 150,
                  width: double.infinity,
                  child: p.coverImage.isEmpty
                      ? Container(
                          color: const Color(0xFFFEF3C7),
                          child: const Center(
                            child: Text("🛕", style: TextStyle(fontSize: 50)),
                          ),
                        )
                      : CachedNetworkImage(
                          imageUrl: MiscApi.imageUrl(p.coverImage),
                          fit: BoxFit.cover,
                          errorWidget: (_, _, _) => Container(
                            color: const Color(0xFFFEF3C7),
                            child: const Center(
                              child: Text("🛕", style: TextStyle(fontSize: 50)),
                            ),
                          ),
                        ),
                ),
                Positioned(
                  top: 10,
                  left: 10,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: const Color(0xFFD97706),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Row(
                      children: [
                        Text("⭐ ", style: TextStyle(fontSize: 10)),
                        Text(
                          "Vedic Mahapuja",
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 10,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),

            // Card Content
            Padding(
              padding: const EdgeInsets.all(14.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    p.title?.toString() ?? 'Vedic Mahapuja',
                    style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w800,
                      color: Color(0xFF0F172A),
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 6),

                  // Teerth Location
                  Row(
                    children: [
                      const Icon(Icons.temple_hindu_rounded, size: 13, color: Color(0xFFD97706)),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          place,
                          style: const TextStyle(fontSize: 11.5, color: Color(0xFF475569)),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 4),

                  // Vedic Tithi
                  Row(
                    children: [
                      const Icon(Icons.calendar_month_rounded, size: 13, color: Color(0xFF0284C7)),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          tithi,
                          style: const TextStyle(fontSize: 11.5, color: Color(0xFF475569)),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),

                  // Price & CTA Button
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text("Sankalp Starts", style: TextStyle(fontSize: 9, color: Colors.grey)),
                          Text(
                            "₹${price.toStringAsFixed(0)}",
                            style: const TextStyle(
                              fontSize: 16.5,
                              fontWeight: FontWeight.w900,
                              color: Color(0xFFD97706),
                            ),
                          ),
                        ],
                      ),
                      ElevatedButton(
                        onPressed: () => Navigator.of(context).push(
                          MaterialPageRoute(builder: (_) => PujaDetailScreen(puja: p)),
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF059669),
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 9),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(20),
                          ),
                        ),
                        child: const Row(
                          children: [
                            Text(
                              "PARTICIPATE",
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 12,
                                fontWeight: FontWeight.w800,
                              ),
                            ),
                            SizedBox(width: 4),
                            Icon(Icons.arrow_forward_rounded, size: 14, color: Colors.white),
                          ],
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
