import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:url_launcher/url_launcher.dart';
import '../services/growth_ai_os_service.dart';

/// Luxury sacred sharing modal sheet for viral social media distribution.
/// Empowers devotees to share Kundlis, Panchang, Horoscopes, Puja updates, and Live Darshan
/// directly across WhatsApp, Telegram, X, Facebook, and Instagram with 1 tap.
class SacredShareSheet extends StatefulWidget {
  final String title;
  final String subtitle;
  final String shareText;
  final String? shareUrl;
  final String? category;
  final String? referralCode;

  const SacredShareSheet({
    super.key,
    required this.title,
    required this.subtitle,
    required this.shareText,
    this.shareUrl,
    this.category,
    this.referralCode,
  });

  /// Static helper to trigger the share sheet anywhere in the application.
  static Future<void> show(
    BuildContext context, {
    required String title,
    required String subtitle,
    required String shareText,
    String? shareUrl,
    String? category,
    String? referralCode,
  }) {
    HapticFeedback.lightImpact();
    return showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) => SacredShareSheet(
        title: title,
        subtitle: subtitle,
        shareText: shareText,
        shareUrl: shareUrl,
        category: category,
        referralCode: referralCode,
      ),
    );
  }

  @override
  State<SacredShareSheet> createState() => _SacredShareSheetState();
}

class _SacredShareSheetState extends State<SacredShareSheet> {
  bool _includeReferral = true;

  String get _finalMessage {
    final os = GrowthAiOsService.instance;
    final bonus = os.socialConfig.referralBonusAmount;
    final code = widget.referralCode ?? 'PUJA2026';

    if (_includeReferral) {
      return '${widget.shareText}\n\n'
          '🎁 *Special Blessing:* Use invite code *$code* for ₹$bonus FREE Puja Wallet bonus & ₹1 First Astrologer Consultation:\n'
          '${widget.shareUrl ?? "https://onlinepuja.live"}';
    }
    return widget.shareText;
  }

  Future<void> _launchIntent(Uri uri, String platform) async {
    HapticFeedback.mediumImpact();
    try {
      final launched =
          await launchUrl(uri, mode: LaunchMode.externalApplication);
      if (!launched && mounted) {
        await launchUrl(uri, mode: LaunchMode.inAppBrowserView);
      }
    } catch (_) {
      if (mounted) {
        Clipboard.setData(ClipboardData(text: _finalMessage));
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            behavior: SnackBarBehavior.floating,
            content: Text(
                'Could not launch $platform directly. Message copied to clipboard!'),
          ),
        );
      }
    }
  }

  void _copyToClipboard() {
    HapticFeedback.lightImpact();
    Clipboard.setData(ClipboardData(text: _finalMessage));
    Navigator.pop(context);
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        backgroundColor: Color(0xFF10B981),
        behavior: SnackBarBehavior.floating,
        content: Row(
          children: [
            Icon(Icons.check_circle_rounded, color: Colors.white),
            SizedBox(width: 8),
            Text('Copied to clipboard! Share anywhere with family & friends.',
                style: TextStyle(fontWeight: FontWeight.bold)),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final os = GrowthAiOsService.instance;

    return Container(
      padding: EdgeInsets.only(
        top: 16,
        left: 20,
        right: 20,
        bottom: MediaQuery.of(context).viewInsets.bottom + 24,
      ),
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF0F172A) : Colors.white,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(28)),
        boxShadow: const [
          BoxShadow(color: Colors.black26, blurRadius: 20, spreadRadius: 4),
        ],
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Drag handle
          Center(
            child: Container(
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: isDark ? Colors.white24 : Colors.black12,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
          ),
          const SizedBox(height: 16),

          // Header
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFFD97706), Color(0xFFB45309)],
                  ),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(Icons.share_rounded,
                    color: Colors.white, size: 20),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      widget.title,
                      style: const TextStyle(
                          fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                    Text(
                      widget.subtitle,
                      style: TextStyle(
                        fontSize: 12,
                        color: isDark ? Colors.white60 : Colors.black54,
                      ),
                    ),
                  ],
                ),
              ),
              IconButton(
                icon: const Icon(Icons.close_rounded, size: 20),
                onPressed: () => Navigator.pop(context),
              ),
            ],
          ),

          const SizedBox(height: 16),

          // Message Preview Container
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: isDark ? const Color(0xFF1E293B) : const Color(0xFFF8FAFC),
              borderRadius: BorderRadius.circular(14),
              border: Border.all(
                color: isDark ? const Color(0xFF334155) : const Color(0xFFE2E8F0),
              ),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    const Icon(Icons.preview_rounded,
                        size: 14, color: Color(0xFFD97706)),
                    const SizedBox(width: 6),
                    Text(
                      'PREVIEW MESSAGE',
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.w900,
                        letterSpacing: 0.5,
                        color: isDark ? Colors.white60 : Colors.black54,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Text(
                  _finalMessage,
                  maxLines: 5,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontSize: 12, height: 1.4),
                ),
              ],
            ),
          ),

          const SizedBox(height: 12),

          // Devotee Referral Booster Switch
          SwitchListTile(
            dense: true,
            contentPadding: EdgeInsets.zero,
            title: Text(
              'Attach My Referral Bonus (+₹${os.socialConfig.referralBonusAmount})',
              style:
                  const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
            ),
            subtitle: const Text(
              'Friends get ₹1 consultation & you get wallet cash upon their booking',
              style: TextStyle(fontSize: 11),
            ),
            value: _includeReferral,
            activeColor: const Color(0xFFD97706),
            onChanged: (val) => setState(() => _includeReferral = val),
          ),

          const SizedBox(height: 16),

          const Text(
            'Share Directly to Social Platforms:',
            style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 12),

          // 1-Tap Social Buttons Row
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _socialChannelButton(
                icon: Icons.chat_rounded,
                color: const Color(0xFF25D366),
                label: 'WhatsApp',
                onTap: () => _launchIntent(
                  SocialContentGenerator.buildWhatsAppUrl(_finalMessage),
                  'WhatsApp',
                ),
              ),
              _socialChannelButton(
                icon: Icons.send_rounded,
                color: const Color(0xFF0088CC),
                label: 'Telegram',
                onTap: () => _launchIntent(
                  SocialContentGenerator.buildTelegramUrl(
                    text: _finalMessage,
                    url: widget.shareUrl,
                  ),
                  'Telegram',
                ),
              ),
              _socialChannelButton(
                icon: Icons.tag_rounded,
                color: const Color(0xFF1DA1F2),
                label: 'X (Twitter)',
                onTap: () => _launchIntent(
                  SocialContentGenerator.buildTwitterUrl(
                    text: _finalMessage,
                    url: widget.shareUrl,
                  ),
                  'X (Twitter)',
                ),
              ),
              _socialChannelButton(
                icon: Icons.facebook_rounded,
                color: const Color(0xFF1877F2),
                label: 'Facebook',
                onTap: () => _launchIntent(
                  SocialContentGenerator.buildFacebookUrl(
                    widget.shareUrl ?? 'https://onlinepuja.live',
                  ),
                  'Facebook',
                ),
              ),
              _socialChannelButton(
                icon: Icons.copy_rounded,
                color: Colors.grey.shade700,
                label: 'Copy Link',
                onTap: _copyToClipboard,
              ),
            ],
          ),

          const SizedBox(height: 20),

          // Official Social Community Link Bar
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
            decoration: BoxDecoration(
              color: const Color(0xFFD97706).withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(
                color: const Color(0xFFD97706).withValues(alpha: 0.25),
              ),
            ),
            child: Row(
              children: [
                const Icon(Icons.campaign_rounded,
                    color: Color(0xFFD97706), size: 18),
                const SizedBox(width: 8),
                const Expanded(
                  child: Text(
                    'Join 50,000+ Devotees on WhatsApp Channel',
                    style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.bold),
                  ),
                ),
                TextButton(
                  style: TextButton.styleFrom(
                    visualDensity: VisualDensity.compact,
                    foregroundColor: const Color(0xFFD97706),
                  ),
                  onPressed: () => launchUrl(
                    Uri.parse(os.socialConfig.whatsappChannelUrl),
                    mode: LaunchMode.externalApplication,
                  ),
                  child: const Text('Join Now',
                      style: TextStyle(fontWeight: FontWeight.w900)),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _socialChannelButton({
    required IconData icon,
    required Color color,
    required String label,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Column(
        children: [
          CircleAvatar(
            radius: 24,
            backgroundColor: color.withValues(alpha: 0.15),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(height: 6),
          Text(
            label,
            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
          ),
        ],
      ),
    );
  }
}
