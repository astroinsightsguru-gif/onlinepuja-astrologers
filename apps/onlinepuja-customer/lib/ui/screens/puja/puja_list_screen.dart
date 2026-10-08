import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

import 'puja_detail_screen.dart';
import '../history/history_screen.dart';

/// Sri Mandir & DevDarshan Grade Puja & Chadhava Experience
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
  String _searchQuery = "";
  bool _isPlaying = true;

  final TextEditingController _searchController = TextEditingController();

  final List<Map<String, String>> _deityPills = [
    {"id": "All", "label": "All Pujas", "icon": "ॐ"},
    {"id": "Durga", "label": "Maa Durga", "icon": "🌺"},
    {"id": "Shiva", "label": "Mahadev Shiva", "icon": "🔱"},
    {"id": "Ganesh", "label": "Lord Ganesh", "icon": "🐘"},
    {"id": "Lakshmi", "label": "Maa Lakshmi", "icon": "🪔"},
    {"id": "Vishnu", "label": "Bhagwan Vishnu", "icon": "🪷"},
    {"id": "Shradh Special", "label": "Pitru Paksha", "icon": "🪶"},
    {"id": "Navagraha", "label": "Graha Shanti", "icon": "🪐"},
  ];

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
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

  static String _formatCategoryTitle(String raw) {
    if (raw.isEmpty) return raw;
    final words = raw.toLowerCase().split(' ');
    return words.map((w) => w.isNotEmpty ? '${w[0].toUpperCase()}${w.substring(1)}' : '').join(' ');
  }

  List<Puja> get _filteredItems {
    if (_items == null) return const [];
    var list = _items!;

    // 1. Deity filter
    if (_selectedDeity != "All") {
      final q = _selectedDeity.toLowerCase();
      list = list.where((p) {
        final title = (p.title ?? '').toLowerCase();
        final sub = (p.subtitle ?? '').toLowerCase();
        final desc = (p.longDescription ?? '').toString().toLowerCase();
        final combined = '$title $sub $desc';
        if (q == 'durga') {
          return combined.contains('durga') || combined.contains('navratri') || combined.contains('kanya') || combined.contains('devi') || combined.contains('saptashati');
        }
        if (q == 'shiva') {
          return combined.contains('shiva') || combined.contains('rudra') || combined.contains('mrityunjay') || combined.contains('mahadev') || combined.contains('kashi') || combined.contains('lingam');
        }
        if (q == 'ganesh') {
          return combined.contains('ganesh') || combined.contains('vinayak') || combined.contains('ganpati') || combined.contains('modak');
        }
        if (q == 'lakshmi') {
          return combined.contains('lakshmi') || combined.contains('laxmi') || combined.contains('kubera') || combined.contains('dhanteras') || combined.contains('diwali');
        }
        if (q == 'vishnu') {
          return combined.contains('vishnu') || combined.contains('satyanarayan') || combined.contains('krishna') || combined.contains('ram') || combined.contains('ekadashi');
        }
        if (q.contains('shradh')) {
          return combined.contains('shradh') || combined.contains('pitru') || combined.contains('tarpan') || combined.contains('gaya');
        }
        if (q == 'navagraha') {
          return combined.contains('navagraha') || combined.contains('graha') || combined.contains('shanti') || combined.contains('dosh') || combined.contains('mangal') || combined.contains('kaal sarp');
        }
        return combined.contains(q);
      }).toList();
    }

    // 2. Search query filter
    if (_searchQuery.trim().isNotEmpty) {
      final term = _searchQuery.trim().toLowerCase();
      list = list.where((p) {
        final title = (p.title ?? '').toLowerCase();
        final sub = (p.subtitle ?? '').toLowerCase();
        final place = (p.place ?? '').toString().toLowerCase();
        return title.contains(term) || sub.contains(term) || place.contains(term);
      }).toList();
    }

    return list;
  }

  @override
  Widget build(BuildContext context) {
    final displayItems = _filteredItems;

    return Scaffold(
      backgroundColor: const Color(0xFFFBF9F5),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0.5,
        titleSpacing: 16,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                RichText(text: const TextSpan(children: [TextSpan(text: 'OnlinePuja', style: TextStyle(fontSize: 16.5, fontWeight: FontWeight.w900, color: Color(0xFF1E293B), letterSpacing: -0.2)), TextSpan(text: '.live', style: TextStyle(fontSize: 16.5, fontWeight: FontWeight.w900, color: Color(0xFFD97706), letterSpacing: -0.2))])),
                SizedBox(width: 6),
                Text('🛕', style: TextStyle(fontSize: 15)),
              ],
            ),
            SizedBox(height: 1),
            Text(
              'Vedic Sankalp at 51 Holy Teerths & Shaktipeeths',
              style: TextStyle(fontSize: 10.5, color: Color(0xFF64748B), fontWeight: FontWeight.w500),
            ),
          ],
        ),
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: 14),
            child: InkWell(
              onTap: () {
                Navigator.of(context).push(
                  MaterialPageRoute(builder: (_) => const HistoryScreen()),
                );
              },
              borderRadius: BorderRadius.circular(20),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 11, vertical: 6),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFFFEF3C7), Color(0xFFFDE68A)],
                  ),
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: const Color(0xFFF59E0B).withValues(alpha: 0.5)),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: const [
                    Text("🛍️", style: TextStyle(fontSize: 12)),
                    SizedBox(width: 4),
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
          : RefreshIndicator(
              onRefresh: _load,
              color: const Color(0xFFD97706),
              child: CustomScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                slivers: [
                  // 1. Festive Hero Mahotsav Banner
                  SliverToBoxAdapter(child: _buildFestiveHeroBanner()),

                  // 2. Devotional Ambient Chant Bar (Compact & Sleek)
                  SliverToBoxAdapter(child: _buildAmbientAudioBar()),

                  // 3. Search Bar
                  SliverToBoxAdapter(child: _buildSearchBar()),

                  // 4. Custom Deity Filter Pills (Zero Overflow)
                  SliverToBoxAdapter(child: _buildDeityFilterStrip()),

                  // 5. Category Chips (Title Cased, Clean)
                  if (_categories != null && _categories!.isNotEmpty)
                    SliverToBoxAdapter(child: _buildCategoryStrip()),

                  // 6. Section Header
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.fromLTRB(16, 12, 16, 6),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
                            children: [
                              Container(
                                width: 4,
                                height: 16,
                                decoration: BoxDecoration(
                                  color: const Color(0xFFD97706),
                                  borderRadius: BorderRadius.circular(2),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Text(
                                _selectedDeity == "All" ? "Upcoming Auspicious Pujas" : "$_selectedDeity Special Pujas",
                                style: const TextStyle(
                                  fontSize: 14.5,
                                  fontWeight: FontWeight.w800,
                                  color: Color(0xFF1E293B),
                                ),
                              ),
                            ],
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                            decoration: BoxDecoration(
                              color: const Color(0xFFF1F5F9),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Text(
                              "${displayItems.length} Available",
                              style: const TextStyle(
                                fontSize: 10.5,
                                fontWeight: FontWeight.w700,
                                color: Color(0xFF64748B),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),

                  // 7. Sacred Puja Cards Feed
                  if (_items == null)
                    const SliverPadding(
                      padding: EdgeInsets.all(60),
                      sliver: SliverToBoxAdapter(
                        child: Center(
                          child: CircularProgressIndicator(strokeWidth: 2.6, color: Color(0xFFD97706)),
                        ),
                      ),
                    )
                  else if (displayItems.isEmpty)
                    SliverToBoxAdapter(
                      child: Container(
                        height: 280,
                        alignment: Alignment.center,
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Text("🪔", style: TextStyle(fontSize: 48)),
                            const SizedBox(height: 12),
                            Text(
                              'No pujas found for "$_selectedDeity"',
                              style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: Color(0xFF475569)),
                            ),
                            const SizedBox(height: 6),
                            const Text(
                              'Try selecting "All Pujas" or clear search filter',
                              style: TextStyle(fontSize: 12, color: Color(0xFF94A3B8)),
                            ),
                            const SizedBox(height: 16),
                            TextButton.icon(
                              onPressed: () {
                                setState(() {
                                  _selectedDeity = "All";
                                  _categoryId = null;
                                  _searchQuery = "";
                                  _searchController.clear();
                                });
                              },
                              icon: const Icon(Icons.refresh_rounded, size: 16),
                              label: const Text("Reset All Filters"),
                              style: TextButton.styleFrom(foregroundColor: const Color(0xFFD97706)),
                            ),
                          ],
                        ),
                      ),
                    )
                  else
                    SliverPadding(
                      padding: const EdgeInsets.fromLTRB(14, 8, 14, 120),
                      sliver: SliverList.separated(
                        itemCount: displayItems.length,
                        separatorBuilder: (_, _) => const SizedBox(height: 18),
                        itemBuilder: (context, i) => _buildSriMandirPujaCard(context, displayItems[i]),
                      ),
                    ),
                ],
              ),
            ),
    );
  }

  // ---------------- WIDGETS ----------------

  /// Festive Hero Banner with rich spiritual gradient, live counters & trust points
  Widget _buildFestiveHeroBanner() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(14, 12, 14, 8),
      child: Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(20),
          gradient: const LinearGradient(
            colors: [Color(0xFF6B2606), Color(0xFF92400E), Color(0xFFB45309)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF78350F).withValues(alpha: 0.28),
              blurRadius: 12,
              offset: const Offset(0, 5),
            ),
          ],
        ),
        child: Stack(
          children: [
            // Om Watermark
            Positioned(
              right: -14,
              bottom: -22,
              child: Opacity(
                opacity: 0.13,
                child: const Text("ॐ", style: TextStyle(fontSize: 160, color: Colors.white, fontWeight: FontWeight.w100)),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 4),
                        decoration: BoxDecoration(
                          color: const Color(0xFFFBBF24),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: const Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Text("✨ ", style: TextStyle(fontSize: 10)),
                            Text(
                              "VEDIC SANKALPA & SEVA",
                              style: TextStyle(
                                fontSize: 9.5,
                                fontWeight: FontWeight.w900,
                                color: Color(0xFF78350F),
                                letterSpacing: 0.5,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.18),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Text("⚡ 100% Certified Pandits", style: TextStyle(color: Colors.white, fontSize: 9.5, fontWeight: FontWeight.w700)),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    "Navratri & Shradh Mahotsav 2026",
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 17,
                      fontWeight: FontWeight.w900,
                      letterSpacing: -0.2,
                      height: 1.2,
                    ),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    "Participate in Sacred Pujas at Kashi Vishwanath, Gaya & Holy Teerths with personalized Sankalp.",
                    style: TextStyle(color: Color(0xFFFEF3C7), fontSize: 11, height: 1.35),
                  ),
                  const SizedBox(height: 12),
                  // Trust pills strip
                  Row(
                    children: [
                      _heroTrustBadge("📹 Video Proof"),
                      const SizedBox(width: 8),
                      _heroTrustBadge("🌸 Gotra Chanting"),
                      const SizedBox(width: 8),
                      _heroTrustBadge("📦 Home Prasad"),
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

  Widget _heroTrustBadge(String text) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
      decoration: BoxDecoration(
        color: Colors.black.withValues(alpha: 0.22),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: Colors.white.withValues(alpha: 0.15)),
      ),
      child: Text(
        text,
        style: const TextStyle(color: Colors.white, fontSize: 9.5, fontWeight: FontWeight.w600),
      ),
    );
  }

  /// Compact Devotional Ambient Chant Bar
  Widget _buildAmbientAudioBar() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        decoration: BoxDecoration(
          color: const Color(0xFF1E293B),
          borderRadius: BorderRadius.circular(14),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.1),
              blurRadius: 8,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              width: 32,
              height: 32,
              decoration: const BoxDecoration(
                shape: BoxShape.circle,
                gradient: LinearGradient(
                  colors: [Color(0xFFF59E0B), Color(0xFFD97706)],
                ),
              ),
              child: const Center(
                child: Text("🕉️", style: TextStyle(fontSize: 16)),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: const [
                  Text(
                    "Vakratunda Mahakaya • Sri Mandir Dhyaan",
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 11,
                      fontWeight: FontWeight.w700,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  SizedBox(height: 1),
                  Text(
                    "Devotional Chants • 4,280 devotees listening",
                    style: TextStyle(color: Color(0xFF94A3B8), fontSize: 9.5),
                  ),
                ],
              ),
            ),
            IconButton(
              padding: EdgeInsets.zero,
              constraints: const BoxConstraints(minWidth: 32, minHeight: 32),
              icon: Icon(
                _isPlaying ? Icons.pause_circle_filled_rounded : Icons.play_circle_filled_rounded,
                color: const Color(0xFFFBBF24),
                size: 26,
              ),
              onPressed: () {
                setState(() => _isPlaying = !_isPlaying);
              },
            ),
          ],
        ),
      ),
    );
  }

  /// Instant Live Search Bar
  Widget _buildSearchBar() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(14, 8, 14, 6),
      child: Container(
        height: 42,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: const Color(0xFFE2E8F0)),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.02),
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: TextField(
          controller: _searchController,
          onChanged: (v) => setState(() => _searchQuery = v),
          style: const TextStyle(fontSize: 12.5),
          decoration: InputDecoration(
            hintText: "Search Puja, Deity, or Holy Dham (Kashi, Gaya...)",
            hintStyle: const TextStyle(fontSize: 11.5, color: Color(0xFF94A3B8)),
            prefixIcon: const Icon(Icons.search_rounded, size: 18, color: Color(0xFFD97706)),
            suffixIcon: _searchQuery.isNotEmpty
                ? IconButton(
                    icon: const Icon(Icons.clear_rounded, size: 16, color: Color(0xFF94A3B8)),
                    onPressed: () {
                      _searchController.clear();
                      setState(() => _searchQuery = "");
                    },
                  )
                : null,
            border: InputBorder.none,
            contentPadding: const EdgeInsets.symmetric(vertical: 10),
          ),
        ),
      ),
    );
  }

  /// Custom Deity Filter Strip (Zero Overflow, Animated, Elegant)
  Widget _buildDeityFilterStrip() {
    return SizedBox(
      height: 44,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
        itemCount: _deityPills.length,
        separatorBuilder: (_, _) => const SizedBox(width: 8),
        itemBuilder: (context, index) {
          final item = _deityPills[index];
          final id = item["id"]!;
          final isSelected = _selectedDeity == id;

          return InkWell(
            onTap: () => setState(() => _selectedDeity = id),
            borderRadius: BorderRadius.circular(20),
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 180),
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                gradient: isSelected
                    ? const LinearGradient(
                        colors: [Color(0xFFD97706), Color(0xFFB45309)],
                      )
                    : null,
                color: isSelected ? null : Colors.white,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(
                  color: isSelected ? const Color(0xFFD97706) : const Color(0xFFE2E8F0),
                  width: 1.2,
                ),
                boxShadow: isSelected
                    ? [
                        BoxShadow(
                          color: const Color(0xFFD97706).withValues(alpha: 0.3),
                          blurRadius: 6,
                          offset: const Offset(0, 2),
                        ),
                      ]
                    : null,
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(item["icon"]!, style: const TextStyle(fontSize: 12.5)),
                  const SizedBox(width: 5),
                  Text(
                    item["label"]!,
                    style: TextStyle(
                      fontSize: 11.5,
                      fontWeight: isSelected ? FontWeight.w800 : FontWeight.w600,
                      color: isSelected ? Colors.white : const Color(0xFF334155),
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  /// Secondary Category Horizontal List (Title-Cased, Clean)
  Widget _buildCategoryStrip() {
    return SizedBox(
      height: 38,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 2),
        itemCount: _categories!.length,
        separatorBuilder: (_, _) => const SizedBox(width: 8),
        itemBuilder: (context, i) {
          final c = _categories![i];
          final isSelected = _categoryId == c.id;
          final title = _formatCategoryTitle(c.name?.toString() ?? '');

          return InkWell(
            onTap: () => _pickCategory(c.id),
            borderRadius: BorderRadius.circular(16),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 11, vertical: 5),
              decoration: BoxDecoration(
                color: isSelected ? const Color(0xFFFEF3C7) : Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(
                  color: isSelected ? const Color(0xFFF59E0B) : const Color(0xFFCBD5E1),
                ),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  if (isSelected) ...[
                    const Icon(Icons.check_circle_rounded, size: 12, color: Color(0xFFD97706)),
                    const SizedBox(width: 4),
                  ],
                  Text(
                    title,
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: isSelected ? FontWeight.w800 : FontWeight.w500,
                      color: isSelected ? const Color(0xFF92400E) : const Color(0xFF475569),
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  /// High Quality Sri Mandir-Grade Puja Card
  Widget _buildSriMandirPujaCard(BuildContext context, Puja p) {
    final price = p.startingPrice ?? 501;
    final strikePrice = (price * 2.2).round();
    final place = (p.place != null && p.place.toString().isNotEmpty)
        ? p.place.toString()
        : "Holy Pilgrimage Teerth";
    final tithi = _formatTithi(p.startDatetime);

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 14,
            offset: const Offset(0, 5),
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
            // 1. Cover Image with Sacred Gradient & Badges
            Stack(
              children: [
                SizedBox(
                  height: 165,
                  width: double.infinity,
                  child: _buildPujaCoverImage(p),
                ),
                // Gradient Scrim for contrast
                Positioned.fill(
                  child: DecoratedBox(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [
                          Colors.black.withValues(alpha: 0.45),
                          Colors.transparent,
                          Colors.black.withValues(alpha: 0.8),
                        ],
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                        stops: const [0.0, 0.4, 1.0],
                      ),
                    ),
                  ),
                ),
                // Top Badges
                Positioned(
                  top: 10,
                  left: 10,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFFD97706), Color(0xFFB45309)],
                      ),
                      borderRadius: BorderRadius.circular(10),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.2),
                          blurRadius: 4,
                        ),
                      ],
                    ),
                    child: const Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text("⭐ ", style: TextStyle(fontSize: 10)),
                        Text(
                          "Vedic Mahapuja",
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 10,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                Positioned(
                  top: 10,
                  right: 10,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.black.withValues(alpha: 0.6),
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: Colors.white.withValues(alpha: 0.2)),
                    ),
                    child: const Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text("🔥 1,280+ Devotees", style: TextStyle(color: Color(0xFFFDE68A), fontSize: 9.5, fontWeight: FontWeight.w700)),
                      ],
                    ),
                  ),
                ),
                // Bottom Date Pill on Image
                Positioned(
                  bottom: 10,
                  left: 12,
                  right: 12,
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: const Color(0xFF0284C7).withValues(alpha: 0.9),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const Icon(Icons.calendar_month_rounded, size: 11, color: Colors.white),
                            const SizedBox(width: 4),
                            Text(
                              tithi,
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 10,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),

            // 2. Card Content
            Padding(
              padding: const EdgeInsets.all(14.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Title
                  Text(
                    p.title?.toString() ?? 'Vedic Mahapuja',
                    style: const TextStyle(
                      fontSize: 15.5,
                      fontWeight: FontWeight.w900,
                      color: Color(0xFF0F172A),
                      letterSpacing: -0.2,
                      height: 1.25,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 5),

                  // Subtitle
                  if (p.subtitle != null && p.subtitle.toString().isNotEmpty) ...[
                    Text(
                      p.subtitle.toString(),
                      style: const TextStyle(fontSize: 11, color: Color(0xFF64748B), height: 1.3),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 6),
                  ],

                  // Teerth Location
                  Row(
                    children: [
                      const Icon(Icons.temple_hindu_rounded, size: 14, color: Color(0xFFD97706)),
                      const SizedBox(width: 5),
                      Expanded(
                        child: Text(
                          place,
                          style: const TextStyle(fontSize: 11.5, fontWeight: FontWeight.w600, color: Color(0xFF334155)),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 10),

                  // 3 Key Guarantees Pill Strip
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                    decoration: BoxDecoration(
                      color: const Color(0xFFF8FAFC),
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: const Color(0xFFE2E8F0)),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceAround,
                      children: const [
                        _PujaPerk(icon: "📹", label: "Video Proof"),
                        _PujaPerk(icon: "🌸", label: "Gotra Chanted"),
                        _PujaPerk(icon: "📦", label: "Home Prasad"),
                      ],
                    ),
                  ),
                  const SizedBox(height: 12),

                  // Price & CTA Row
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              const Text("Sankalp Starts ", style: TextStyle(fontSize: 9.5, color: Color(0xFF94A3B8))),
                              Text(
                                "₹$strikePrice",
                                style: const TextStyle(
                                  fontSize: 10,
                                  color: Color(0xFF94A3B8),
                                  decoration: TextDecoration.lineThrough,
                                ),
                              ),
                            ],
                          ),
                          Row(
                            children: [
                              Text(
                                "₹${price.toStringAsFixed(0)}",
                                style: const TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.w900,
                                  color: Color(0xFFD97706),
                                ),
                              ),
                              const SizedBox(width: 6),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1.5),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFDCFCE7),
                                  borderRadius: BorderRadius.circular(4),
                                ),
                                child: const Text(
                                  "54% OFF",
                                  style: TextStyle(
                                    fontSize: 8.5,
                                    fontWeight: FontWeight.w800,
                                    color: Color(0xFF15803D),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                      ElevatedButton(
                        onPressed: () => Navigator.of(context).push(
                          MaterialPageRoute(builder: (_) => PujaDetailScreen(puja: p)),
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF059669),
                          elevation: 2,
                          shadowColor: const Color(0xFF059669).withValues(alpha: 0.4),
                          padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 11),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(22),
                          ),
                        ),
                        child: const Row(
                          children: [
                            Text(
                              "PARTICIPATE",
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 12,
                                fontWeight: FontWeight.w900,
                                letterSpacing: 0.3,
                              ),
                            ),
                            SizedBox(width: 5),
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

  /// Resilient multi-tier image loader with spiritual defaults
  Widget _buildPujaCoverImage(Puja p) {
    final rawUrl = p.coverImage.trim();
    final resolvedUrl = rawUrl.isNotEmpty ? MiscApi.imageUrl(rawUrl) : '';

    if (resolvedUrl.isNotEmpty) {
      return Image.network(
        resolvedUrl,
        fit: BoxFit.cover,
        errorBuilder: (context, error, stackTrace) => _buildDivineFallbackImage(p),
        loadingBuilder: (context, child, progress) {
          if (progress == null) return child;
          return Container(
            color: const Color(0xFFFEF3C7),
            child: const Center(
              child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFFD97706)),
            ),
          );
        },
      );
    }

    return _buildDivineFallbackImage(p);
  }

  /// Gorgeous spiritual art fallback when image is unavailable
  Widget _buildDivineFallbackImage(Puja p) {
    final title = (p.title ?? '').toLowerCase();
    String symbol = "ॐ";
    Color topColor = const Color(0xFF78350F);
    Color bottomColor = const Color(0xFFB45309);

    if (title.contains('durga') || title.contains('navratri') || title.contains('kanya')) {
      symbol = "🌺";
      topColor = const Color(0xFF881337);
      bottomColor = const Color(0xFFBE123C);
    } else if (title.contains('shiva') || title.contains('rudra') || title.contains('mrityunjay')) {
      symbol = "🔱";
      topColor = const Color(0xFF0F172A);
      bottomColor = const Color(0xFF334155);
    } else if (title.contains('ganesh') || title.contains('vinayak')) {
      symbol = "🐘";
      topColor = const Color(0xFF7C2D12);
      bottomColor = const Color(0xFFEA580C);
    } else if (title.contains('lakshmi') || title.contains('kubera') || title.contains('dhanteras')) {
      symbol = "🪔";
      topColor = const Color(0xFF713F12);
      bottomColor = const Color(0xFFCA8A04);
    } else if (title.contains('shradh') || title.contains('pitru') || title.contains('gaya')) {
      symbol = "🪶";
      topColor = const Color(0xFF1C1917);
      bottomColor = const Color(0xFF44403C);
    }

    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [topColor, bottomColor],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
      ),
      child: Stack(
        children: [
          Positioned(
            right: 20,
            bottom: -10,
            child: Opacity(
              opacity: 0.15,
              child: const Text("ॐ", style: TextStyle(fontSize: 130, color: Colors.white)),
            ),
          ),
          Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(symbol, style: const TextStyle(fontSize: 42)),
                const SizedBox(height: 6),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.2),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: const Text(
                    "Sacred Teerth Seva",
                    style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w700),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _PujaPerk extends StatelessWidget {
  const _PujaPerk({required this.icon, required this.label});
  final String icon;
  final String label;

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Text(icon, style: const TextStyle(fontSize: 10.5)),
        const SizedBox(width: 4),
        Text(
          label,
          style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: Color(0xFF475569)),
        ),
      ],
    );
  }
}

