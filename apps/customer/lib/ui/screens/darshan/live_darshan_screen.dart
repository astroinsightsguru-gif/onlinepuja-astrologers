import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:op_shared/op_shared.dart';
import 'package:url_launcher/url_launcher.dart';

/// Represents a sacred temple live darshan feed.
class TempleFeed {
  final String id;
  final String name;
  final String deity;
  final String location;
  final String timing;
  final String thumbnailUrl;
  final String streamUrl;
  final String description;

  const TempleFeed({
    required this.id,
    required this.name,
    required this.deity,
    required this.location,
    required this.timing,
    required this.thumbnailUrl,
    required this.streamUrl,
    required this.description,
  });
}

/// Live Temple Darshan & Virtual Aarti Hub.
/// Allows devotees to watch live feeds of Kashi Vishwanath, Ganga Aarti, Mahakal,
/// and offer virtual Flowers, Diya, and Bell chimes with real-time animations.
class LiveDarshanScreen extends StatefulWidget {
  const LiveDarshanScreen({super.key});

  static const route = '/live-darshan';

  @override
  State<LiveDarshanScreen> createState() => _LiveDarshanScreenState();
}

class _LiveDarshanScreenState extends State<LiveDarshanScreen> with SingleTickerProviderStateMixin {
  late TempleFeed _selectedFeed;
  int _flowerCount = 0;
  bool _diyaLit = false;
  int _bellChimes = 0;
  final List<Offset> _floatingFlowers = [];
  Timer? _flowerCleanup;

  static const List<TempleFeed> _templeFeeds = [
    TempleFeed(
      id: 'kashi',
      name: 'Shri Kashi Vishwanath Jyotirlinga',
      deity: 'Lord Shiva',
      location: 'Varanasi, Uttar Pradesh',
      timing: 'Mangala Aarti 3:00 AM · Sandhya 7:00 PM',
      thumbnailUrl: 'https://images.unsplash.com/photo-1561361513-2d000a50f0dc?auto=format&fit=crop&w=800&q=80',
      streamUrl: 'https://www.youtube.com/@ShriKashiVishwanathTempleTrust/live',
      description: 'Official Live Sanctum Darshan of the first Jyotirlinga on the holy banks of river Ganga.',
    ),
    TempleFeed(
      id: 'ganga_aarti',
      name: 'Dashashwamedh Ghat Maha Ganga Aarti',
      deity: 'Maa Ganga',
      location: 'Dashashwamedh Ghat, Varanasi',
      timing: 'Daily Sunset 6:30 PM – 7:30 PM',
      thumbnailUrl: 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=800&q=80',
      streamUrl: 'https://www.youtube.com/@GangaAartiVaranasiOfficial/live',
      description: 'The world-renowned Grand Sunset Aarti with sacred brass deepams, shankha naad, and vedic chants.',
    ),
    TempleFeed(
      id: 'mahakal',
      name: 'Mahakaleshwar Jyotirlinga',
      deity: 'Lord Mahakal',
      location: 'Ujjain, Madhya Pradesh',
      timing: 'Bhasma Aarti 4:00 AM · Shringar 7:30 PM',
      thumbnailUrl: 'https://images.unsplash.com/photo-1609766857041-ed402ea8069a?auto=format&fit=crop&w=800&q=80',
      streamUrl: 'https://www.youtube.com/@shreemahakaleshwarmandiruj790/live',
      description: 'Official Live holy Darshan & Bhasma Aarti of the South-facing Dakshinmukhi Swayambhu Jyotirlinga.',
    ),
    TempleFeed(
      id: 'somnath',
      name: 'Somnath Mahadev Jyotirlinga',
      deity: 'Lord Shiva',
      location: 'Prabhas Patan, Gujarat',
      timing: 'Daily 6:00 AM – 9:30 PM',
      thumbnailUrl: 'https://images.unsplash.com/photo-1621847468516-1ed5d0df56fe?auto=format&fit=crop&w=800&q=80',
      streamUrl: 'https://www.youtube.com/@SomnathTempleOfficial/live',
      description: 'First of the twelve sacred Aadi Jyotirlingas situated on the coast of the Arabian Sea.',
    ),
    TempleFeed(
      id: 'shirdi',
      name: 'Shirdi Sai Baba Samadhi Mandir',
      deity: 'Shirdi Sai Baba',
      location: 'Shirdi, Maharashtra',
      timing: 'Kakad Aarti 4:30 AM · Dhoop 6:00 PM · Shej 10:00 PM',
      thumbnailUrl: 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=800&q=80',
      streamUrl: 'https://www.youtube.com/@SaiBabaSansthanTrustShirdi/live',
      description: '24/7 Live Darshan & Aarti from the sacred Samadhi Mandir of Sai Baba.',
    ),
  ];

  Future<void> _openLiveStream() async {
    HapticFeedback.mediumImpact();
    final uri = Uri.parse(_selectedFeed.streamUrl);
    try {
      final launched = await launchUrl(uri, mode: LaunchMode.externalApplication);
      if (!launched && mounted) {
        await launchUrl(uri, mode: LaunchMode.inAppBrowserView);
      }
    } catch (_) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Opening ${_selectedFeed.name} live stream…')),
        );
      }
    }
  }

  @override
  void initState() {
    super.initState();
    _selectedFeed = _templeFeeds.first;
  }

  @override
  void dispose() {
    _flowerCleanup?.cancel();
    super.dispose();
  }

  void _offerFlowers() {
    HapticFeedback.lightImpact();
    setState(() {
      _flowerCount++;
      _floatingFlowers.add(Offset(
        (0.2 + (0.6 * (DateTime.now().millisecond / 1000))),
        0.8,
      ));
    });

    _flowerCleanup?.cancel();
    _flowerCleanup = Timer(const Duration(seconds: 4), () {
      if (mounted) setState(() => _floatingFlowers.clear());
    });
  }

  void _ringBell() {
    HapticFeedback.mediumImpact();
    setState(() => _bellChimes++);
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        duration: Duration(milliseconds: 1500),
        content: Text('🔔 Temple bell chimed. Divine vibrations awakened!'),
      ),
    );
  }

  void _toggleDiya() {
    HapticFeedback.heavyImpact();
    setState(() => _diyaLit = !_diyaLit);
    if (_diyaLit) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          duration: Duration(seconds: 2),
          content: Text('🪔 Sacred Akhand Diya lit in your name at the sanctum!'),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;

    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        backgroundColor: Colors.black,
        foregroundColor: Colors.white,
        title: const Row(
          children: [
            Text('Live Temple Darshan'),
            SizedBox(width: 8),
            Icon(Icons.fiber_manual_record, color: Colors.redAccent, size: 14),
            SizedBox(width: 4),
            Text('LIVE', style: TextStyle(color: Colors.redAccent, fontSize: 11, fontWeight: FontWeight.bold)),
          ],
        ),
      ),
      body: Column(
        children: [
          // 1. Live Video Stream Container with Interactive Overlays
          Expanded(
            flex: 5,
            child: Stack(
              children: [
                // Video Screen / Sanctum View
                Container(
                  width: double.infinity,
                  height: double.infinity,
                  color: const Color(0xFF0F172A),
                  child: Stack(
                    fit: StackFit.expand,
                    children: [
                      Image.network(
                        _selectedFeed.thumbnailUrl,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) => Container(
                          color: AppTheme.brandDeep,
                          child: const Center(
                            child: Icon(Icons.temple_hindu_rounded, size: 64, color: Colors.white54),
                          ),
                        ),
                      ),
                      // Dark gradient overlay
                      Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                            colors: [
                              Colors.black.withValues(alpha: 0.3),
                              Colors.transparent,
                              Colors.black.withValues(alpha: 0.8),
                            ],
                          ),
                        ),
                      ),
                      // Live broadcast badge & timing
                      Positioned(
                        top: 14,
                        left: 14,
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                          decoration: BoxDecoration(
                            color: Colors.red.withValues(alpha: 0.85),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(Icons.sensors, color: Colors.white, size: 14),
                              SizedBox(width: 5),
                              Text('LIVE SANCTUM FEED', style: TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold)),
                            ],
                          ),
                        ),
                      ),
                      // Lit Diya Overlay if active
                      if (_diyaLit)
                        Positioned(
                          bottom: 24,
                          left: 20,
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                            decoration: BoxDecoration(
                              color: Colors.black.withValues(alpha: 0.65),
                              borderRadius: BorderRadius.circular(20),
                              border: Border.all(color: Colors.amberAccent),
                            ),
                            child: const Row(
                              children: [
                                Text('🪔', style: TextStyle(fontSize: 18)),
                                SizedBox(width: 6),
                                Text(
                                  'Your Diya is Glowing at the Sanctum',
                                  style: TextStyle(color: Colors.amberAccent, fontSize: 12, fontWeight: FontWeight.bold),
                                ),
                              ],
                            ),
                          ),
                        ),
                      // Central Watch Live CTA
                      Center(
                        child: GestureDetector(
                          onTap: _openLiveStream,
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 12),
                            decoration: BoxDecoration(
                              color: Colors.black.withValues(alpha: 0.75),
                              borderRadius: BorderRadius.circular(30),
                              border: Border.all(color: Colors.redAccent.withValues(alpha: 0.9), width: 1.5),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.redAccent.withValues(alpha: 0.4),
                                  blurRadius: 16,
                                  spreadRadius: 2,
                                ),
                              ],
                            ),
                            child: const Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                CircleAvatar(
                                  radius: 16,
                                  backgroundColor: Colors.redAccent,
                                  child: Icon(Icons.play_arrow_rounded, color: Colors.white, size: 22),
                                ),
                                SizedBox(width: 10),
                                Text(
                                  'WATCH LIVE SANCTUM FEED',
                                  style: TextStyle(
                                    color: Colors.white,
                                    fontWeight: FontWeight.bold,
                                    fontSize: 12,
                                    letterSpacing: 0.5,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                      // Animated floating flowers
                      ..._floatingFlowers.map((pos) => Positioned(
                            bottom: 60,
                            left: MediaQuery.of(context).size.width * pos.dx,
                            child: const Text('🌸', style: TextStyle(fontSize: 28)),
                          )),
                    ],
                  ),
                ),

                // Live Aarti Offering Controls Overlay Bar
                Positioned(
                  bottom: 12,
                  right: 14,
                  child: Row(
                    children: [
                      _actionButton(
                        icon: '🔔',
                        label: 'Ring Bell',
                        count: _bellChimes > 0 ? '$_bellChimes' : null,
                        onTap: _ringBell,
                      ),
                      const SizedBox(width: 8),
                      _actionButton(
                        icon: '🌸',
                        label: 'Offer Pushpa',
                        count: _flowerCount > 0 ? '$_flowerCount' : null,
                        onTap: _offerFlowers,
                      ),
                      const SizedBox(width: 8),
                      _actionButton(
                        icon: '🪔',
                        label: _diyaLit ? 'Diya Lit' : 'Light Diya',
                        highlight: _diyaLit,
                        onTap: _toggleDiya,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          // 2. Temple Information & Selector
          Expanded(
            flex: 5,
            child: Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                color: scheme.surface,
                borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              _selectedFeed.name,
                              style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              _selectedFeed.location,
                              style: TextStyle(color: scheme.outline, fontSize: 13),
                            ),
                          ],
                        ),
                      ),
                      FilledButton.icon(
                        style: FilledButton.styleFrom(
                          backgroundColor: AppTheme.brandSaffron,
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ),
                        onPressed: () {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Sankalp & Chadhava recorded! Pandit ji will perform your prayer.')),
                          );
                        },
                        icon: const Icon(Icons.volunteer_activism_rounded, size: 16),
                        label: const Text('Offer Chadhava ₹51'),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text(
                    _selectedFeed.description,
                    style: const TextStyle(fontSize: 12.5, height: 1.4),
                  ),
                  const SizedBox(height: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    decoration: BoxDecoration(
                      color: AppTheme.brandSaffron.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: AppTheme.brandSaffron.withValues(alpha: 0.25)),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.access_time_filled_rounded, size: 16, color: AppTheme.brandDeep),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            'Auspicious Aarti Timings: ${_selectedFeed.timing}',
                            style: const TextStyle(fontSize: 11.5, fontWeight: FontWeight.w600, color: AppTheme.brandDeep),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 10),
                  const Divider(),
                  const SizedBox(height: 6),
                  const Text('Select Holy Destination:', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                  const SizedBox(height: 10),

                  // Horizontal Temple Selector
                  Expanded(
                    child: ListView.separated(
                      scrollDirection: Axis.horizontal,
                      itemCount: _templeFeeds.length,
                      separatorBuilder: (context, index) => const SizedBox(width: 12),
                      itemBuilder: (context, i) {
                        final feed = _templeFeeds[i];
                        final isCurrent = feed.id == _selectedFeed.id;

                        return InkWell(
                          borderRadius: BorderRadius.circular(16),
                          onTap: () {
                            setState(() {
                              _selectedFeed = feed;
                              _flowerCount = 0;
                              _diyaLit = false;
                              _bellChimes = 0;
                            });
                          },
                          child: Container(
                            width: 140,
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: isCurrent
                                  ? AppTheme.brandSaffron.withValues(alpha: 0.15)
                                  : scheme.surfaceContainerHighest.withValues(alpha: 0.3),
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(
                                color: isCurrent ? AppTheme.brandSaffron : scheme.outline.withValues(alpha: 0.2),
                                width: isCurrent ? 2 : 1,
                              ),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                ClipRRect(
                                  borderRadius: BorderRadius.circular(10),
                                  child: Image.network(
                                    feed.thumbnailUrl,
                                    height: 64,
                                    width: double.infinity,
                                    fit: BoxFit.cover,
                                    errorBuilder: (context, error, stackTrace) => Container(
                                      height: 64,
                                      color: AppTheme.brandDeep,
                                      child: const Center(child: Icon(Icons.temple_hindu, color: Colors.white54)),
                                    ),
                                  ),
                                ),
                                const SizedBox(height: 6),
                                Text(
                                  feed.name,
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                  style: TextStyle(
                                    fontWeight: isCurrent ? FontWeight.bold : FontWeight.w600,
                                    fontSize: 11.5,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _actionButton({
    required String icon,
    required String label,
    String? count,
    bool highlight = false,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
        decoration: BoxDecoration(
          color: highlight ? Colors.amberAccent : Colors.black.withValues(alpha: 0.65),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: highlight ? Colors.amber : Colors.white30),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(icon, style: const TextStyle(fontSize: 14)),
            const SizedBox(width: 4),
            Text(
              count != null ? '$label ($count)' : label,
              style: TextStyle(
                color: highlight ? Colors.black : Colors.white,
                fontSize: 11,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
