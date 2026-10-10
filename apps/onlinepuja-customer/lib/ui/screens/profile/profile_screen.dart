import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../state/app_session.dart';
import '../../theme/customer_theme.dart';
import '../../widgets/customer_widgets.dart';
import '../auth/phone_login_screen.dart';
import '../history/history_screen.dart';
import '../kundli/kundli_list_screen.dart';
import '../notifications_screen.dart';
import '../orders/prasadam_tracker_screen.dart';
import '../puja/sankalp_vault_screen.dart';
import 'family_gotra_vault_screen.dart';
import 'wallet_screen.dart';

/// Devotee Profile & Account Sanctum
class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final session = context.watch<AppSession>();
    final u = session.user;
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final currency = session.flags.currency;

    return Scaffold(
      appBar: AppBar(
        title: RichText(text: TextSpan(children: [TextSpan(text: 'OnlinePuja', style: TextStyle(fontSize: 16.5, fontWeight: FontWeight.w900, color: isDark ? Colors.white : const Color(0xFF1E293B), letterSpacing: -0.2)), const TextSpan(text: '.live', style: TextStyle(fontSize: 16.5, fontWeight: FontWeight.w900, color: Color(0xFFD97706), letterSpacing: -0.2)), TextSpan(text: ' - Profile', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: isDark ? Colors.white70 : const Color(0xFF64748B)))])) ,
        actions: [
          IconButton(
            tooltip: 'Notifications',
            icon: const Icon(Icons.notifications_outlined),
            onPressed: () => Navigator.of(context).push(
              MaterialPageRoute(builder: (_) => const NotificationsScreen()),
            ),
          ),
        ],
      ),
      body: Container(
        decoration: BoxDecoration(
          gradient: isDark
              ? CustomerTheme.cosmicDarkGradient
              : const LinearGradient(
                  colors: [
                    Color(0xFFFFFBEB),
                    Color(0xFFFAF7F2),
                  ],
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                ),
        ),
        child: ListView(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          children: [
            // 1. Devotee Hero Card
            _devoteeHeroCard(context, u, session, isDark),
            const SizedBox(height: 16),

            // 2. Quick Action Shortcut Grid
            _quickActionsGrid(context, u, currency),
            const SizedBox(height: 20),

            // 3. Section: Spiritual Services & Orders
            const SectionHeader(
              title: 'Activity & History',
              icon: Icons.history_rounded,
            ),
            const SizedBox(height: 8),
            SacredCard(
              padding: EdgeInsets.zero,
              child: Column(
                children: [
                  _profileListTile(
                    icon: Icons.history_toggle_off_rounded,
                    title: 'Consultation History',
                    subtitle: 'Past chats, calls & recordings',
                    color: CustomerTheme.brandSaffron,
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute(builder: (_) => const HistoryScreen()),
                    ),
                  ),
                  const Divider(height: 1, indent: 56),
                  _profileListTile(
                    icon: Icons.delivery_dining_rounded,
                    title: 'Prasadam & Puja Tracker',
                    subtitle: 'Live status of booked pujas & deliveries',
                    color: CustomerTheme.brandGold,
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute(
                        builder: (_) => const PrasadamTrackerScreen(),
                      ),
                    ),
                  ),
                  const Divider(height: 1, indent: 56),
                  _profileListTile(
                    icon: Icons.video_collection_rounded,
                    title: 'Sankalp Video & Holy Prasad Vault',
                    subtitle: 'Pandit ritual clips, courier AWB tracking & certificates',
                    color: const Color(0xFFD97706),
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute(
                        builder: (_) => const SankalpVaultScreen(),
                      ),
                    ),
                  ),
                  const Divider(height: 1, indent: 56),
                  _profileListTile(
                    icon: Icons.account_tree_rounded,
                    title: 'Family Gotra & Lineage Vault',
                    subtitle: 'Sacred gotra, kuldevta & ancestral names for pujas',
                    color: const Color(0xFF8B5CF6),
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute(
                        builder: (_) => const FamilyGotraVaultScreen(),
                      ),
                    ),
                  ),
                  const Divider(height: 1, indent: 56),
                  _profileListTile(
                    icon: Icons.auto_graph_rounded,
                    title: 'Saved Kundlis',
                    subtitle: 'Your family birth charts & planetary sheets',
                    color: CustomerTheme.sacredEmerald,
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute(
                        builder: (_) => const KundliListScreen(),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Viral Social Referral Card
            SacredCard(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFFD97706), Color(0xFFB45309)],
                      ),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: const Icon(Icons.volunteer_activism_rounded, color: Colors.white, size: 26),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Invite Devotees & Earn ₹51',
                          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14.5),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          'Share blessings with family. They get ₹1 consultation, you receive ₹51 Puja Cash!',
                          style: TextStyle(fontSize: 11.5, color: isDark ? Colors.white60 : Colors.black54),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 8),
                  FilledButton(
                    style: FilledButton.styleFrom(
                      backgroundColor: CustomerTheme.brandGold,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    ),
                    onPressed: () {
                      final devoteeName = u?.name ?? 'A Devotee';
                      final refCode = u != null ? 'PUJA${u.id}' : 'PUJA2026';
                      SacredShareSheet.show(
                        context,
                        title: 'Invite Friends & Family',
                        subtitle: 'Earn ₹51 Puja Wallet Cash per Referral',
                        shareText: SocialContentGenerator.formatReferralInvite(
                          devoteeName: devoteeName,
                          referralCode: refCode,
                          bonusAmount: GrowthAiOsService.instance.socialConfig.referralBonusAmount,
                        ),
                        shareUrl: 'https://onlinepuja.live/invite/$refCode',
                        category: 'Referral',
                        referralCode: refCode,
                      );
                    },
                    child: const Text('Invite', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12.5)),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),



            // 4. Section: Preferences & Appearance
            const SectionHeader(
              title: 'Preferences & Sanctum Settings',
              icon: Icons.tune_rounded,
            ),
            const SizedBox(height: 8),
            SacredCard(
              padding: EdgeInsets.zero,
              child: Column(
                children: [
                  _themeSelectorTile(context, session, isDark),
                  const Divider(height: 1, indent: 56),
                  _profileListTile(
                    icon: Icons.headset_mic_rounded,
                    title: 'Help & 24/7 Devotee Support',
                    subtitle: 'WhatsApp assistance & email inquiries',
                    color: const Color(0xFF0284C7),
                    onTap: () => _showSupport(context),
                  ),
                  const Divider(height: 1, indent: 56),
                  _profileListTile(
                    icon: Icons.policy_rounded,
                    title: 'Privacy Policy & Terms',
                    subtitle: '100% confidential & certified Vedic platform',
                    color: Colors.grey,
                    onTap: () => launchUrl(
                      Uri.parse('https://onlinepuja.live/privacy-policy'),
                      mode: LaunchMode.externalApplication,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // 5. Auth Action: Logout / Login / Delete Account
            if (u != null) ...[
              OutlinedButton.icon(
                style: OutlinedButton.styleFrom(
                  foregroundColor: CustomerTheme.brandCrimson,
                  side: BorderSide(
                    color: CustomerTheme.brandCrimson.withValues(alpha: 0.5),
                  ),
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                ),
                icon: const Icon(Icons.logout_rounded, size: 20),
                label: const Text(
                  'Sign Out from Sanctum',
                  style: TextStyle(fontWeight: FontWeight.w700),
                ),
                onPressed: () => _logout(context, session),
              ),
              const SizedBox(height: 12),
              Center(
                child: TextButton.icon(
                  style: TextButton.styleFrom(
                    foregroundColor: Colors.red.shade400,
                  ),
                  icon: const Icon(Icons.delete_forever_rounded, size: 18),
                  label: const Text(
                    'Delete Devotee Account & Sanctum Data',
                    style: TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                      decoration: TextDecoration.underline,
                    ),
                  ),
                  onPressed: () => _deleteAccount(context, session),
                ),
              ),
            ] else
              SacredButton(
                text: 'Sign In to Online Puja',
                icon: Icons.login_rounded,
                onPressed: () => Navigator.of(context)
                    .pushNamed(PhoneLoginScreen.route),
              ),
            const SizedBox(height: 36),
          ],
        ),
      ),
    );
  }

  Widget _devoteeHeroCard(
    BuildContext context,
    User? u,
    AppSession session,
    bool isDark,
  ) {
    final displayName = u?.displayName ?? '';
    final initial = displayName.isNotEmpty ? displayName[0].toUpperCase() : 'ð-ï¸';

    return SacredCard(
      padding: const EdgeInsets.all(18),
      gradient: isDark
          ? CustomerTheme.heroCosmicGradient
          : const LinearGradient(
              colors: [Color(0xFFFFF7ED), Color(0xFFFFEDD5)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
      elevation: 2,
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(3),
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              gradient: CustomerTheme.goldGradient,
              boxShadow: [
                BoxShadow(
                  color: CustomerTheme.brandGold.withValues(alpha: 0.35),
                  blurRadius: 8,
                ),
              ],
            ),
            child: CircleAvatar(
              radius: 32,
              backgroundColor: CustomerTheme.brandSaffron,
              child: Text(
                initial,
                style: const TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.w800,
                  color: Colors.white,
                ),
              ),
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Flexible(
                      child: Text(
                        displayName.isNotEmpty ? displayName : 'Devotee Guest',
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.w900,
                          color: isDark ? Colors.white : const Color(0xFF1E1A16),
                        ),
                      ),
                    ),
                    const SizedBox(width: 6),
                    const SacredBadge(
                      label: 'DEVOTEE',
                      fontSize: 8.5,
                    ),
                  ],
                ),
                const SizedBox(height: 4),
                Text(
                  u?.contactNo?.isNotEmpty == true
                      ? '${u?.countryCode ?? '+91'} ${u?.contactNo}'
                      : 'Sign in to access your consultations & wallet',
                  style: TextStyle(
                    fontSize: 12,
                    color: isDark ? Colors.white70 : Colors.black54,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _quickActionsGrid(
    BuildContext context,
    User? u,
    String currency,
  ) {
    return Row(
      children: [
        Expanded(
          child: SacredCard(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
            onTap: () => Navigator.of(context).pushNamed(WalletScreen.route),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(6),
                      decoration: BoxDecoration(
                        color: CustomerTheme.sacredEmerald.withValues(alpha: 0.15),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(
                        Icons.account_balance_wallet_rounded,
                        size: 18,
                        color: CustomerTheme.sacredEmerald,
                      ),
                    ),
                    const Spacer(),
                    const Icon(
                      Icons.add_circle_rounded,
                      size: 18,
                      color: CustomerTheme.sacredEmerald,
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                Text(
                  '$currency${(u?.walletAmount ?? 0).toStringAsFixed(0)}',
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w900,
                    color: CustomerTheme.sacredEmerald,
                  ),
                ),
                const SizedBox(height: 2),
                const Text(
                  'Wallet Balance',
                  style: TextStyle(fontSize: 11, color: Colors.grey),
                ),
              ],
            ),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: SacredCard(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
            onTap: () => Navigator.of(context).push(
              MaterialPageRoute(builder: (_) => const HistoryScreen()),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(6),
                      decoration: BoxDecoration(
                        color: CustomerTheme.brandSaffron.withValues(alpha: 0.15),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(
                        Icons.chat_bubble_rounded,
                        size: 18,
                        color: CustomerTheme.brandSaffron,
                      ),
                    ),
                    const Spacer(),
                    const Icon(
                      Icons.arrow_forward_ios_rounded,
                      size: 12,
                      color: Colors.grey,
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                const Text(
                  'Consultations',
                  style: TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                const SizedBox(height: 2),
                const Text(
                  'Chats & Calls',
                  style: TextStyle(fontSize: 11, color: Colors.grey),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _profileListTile({
    required IconData icon,
    required String title,
    required String subtitle,
    required Color color,
    required VoidCallback onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: ListTile(
        onTap: onTap,
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: color.withValues(alpha: 0.15),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, color: color, size: 20),
        ),
        title: Text(
          title,
          style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13.5),
        ),
        subtitle: Text(
          subtitle,
          style: const TextStyle(fontSize: 11.5, color: Colors.grey),
        ),
        trailing: const Icon(
          Icons.chevron_right_rounded,
          size: 20,
          color: Colors.grey,
        ),
      ),
    );
  }

  Widget _themeSelectorTile(
    BuildContext context,
    AppSession session,
    bool isDark,
  ) {
    return Material(
      color: Colors.transparent,
      child: ListTile(
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: CustomerTheme.brandGold.withValues(alpha: 0.15),
            borderRadius: BorderRadius.circular(10),
          ),
          child: const Icon(
            Icons.brightness_medium_rounded,
            color: CustomerTheme.brandGold,
            size: 20,
          ),
        ),
        title: const Text(
          'Sanctum Appearance',
          style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13.5),
        ),
        subtitle: Text(
          session.themeMode == ThemeMode.dark
              ? 'Night Sanctum (Dark Mode)'
              : session.themeMode == ThemeMode.light
                  ? 'Day Sanctum (Light Mode)'
                  : 'System Default',
          style: const TextStyle(fontSize: 11.5, color: Colors.grey),
        ),
        trailing: IconButton(
          icon: Icon(
            session.themeMode == ThemeMode.dark
                ? Icons.dark_mode_rounded
                : Icons.light_mode_rounded,
            color: CustomerTheme.brandSaffron,
          ),
          onPressed: () {
            final next = session.themeMode == ThemeMode.dark
                ? ThemeMode.light
                : ThemeMode.dark;
            session.setThemeMode(next);
          },
        ),
      ),
    );
  }

  void _showSupport(BuildContext context) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.all(22),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Devotee Support & Help',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
            ),
            const SizedBox(height: 6),
            const Text(
              'Our temple coordinators and support staff are here to assist you 24/7.',
              style: TextStyle(fontSize: 13, color: Colors.grey),
            ),
            const SizedBox(height: 18),
            ListTile(
              leading: const Icon(Icons.chat_rounded, color: Colors.green),
              title: const Text('WhatsApp Support'),
              subtitle: Text('${context.read<AppSession>().flags.supportPhone} Â- Instant reply'),
              onTap: () {
                final wa = context.read<AppSession>().flags.supportWhatsapp;
                Navigator.pop(ctx);
                launchUrl(
                  Uri.parse('https://wa.me/$wa'),
                  mode: LaunchMode.externalApplication,
                );
              },
            ),
            ListTile(
              leading: const Icon(Icons.email_outlined, color: Colors.orange),
              title: const Text('Email Support'),
              subtitle: const Text('support@onlinepuja.live'),
              onTap: () {
                Navigator.pop(ctx);
                launchUrl(Uri.parse('mailto:support@onlinepuja.live'));
              },
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _logout(BuildContext context, AppSession session) async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Sign Out'),
        content: const Text('Are you sure you want to sign out of Online Puja?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Cancel'),
          ),
          FilledButton(
            style: FilledButton.styleFrom(
              backgroundColor: CustomerTheme.brandCrimson,
            ),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Sign Out'),
          ),
        ],
      ),
    );
    if (ok == true) {
      await session.logout();
      if (context.mounted) {
        Navigator.of(context).pushNamedAndRemoveUntil(
          PhoneLoginScreen.route,
          (r) => false,
        );
      }
    }
  }

  Future<void> _deleteAccount(BuildContext context, AppSession session) async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Row(
          children: [
            Icon(Icons.warning_amber_rounded, color: Colors.red),
            SizedBox(width: 8),
            Text('Delete Account?'),
          ],
        ),
        content: const Text(
          'This action is irreversible. All your stored Gotra vaults, '
          'Sankalp history, Kundli charts, and wallet balance will be '
          'permanently erased in accordance with data privacy regulations.\n\n'
          'Are you sure you wish to proceed?',
          style: TextStyle(fontSize: 14, height: 1.4),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Keep Account'),
          ),
          FilledButton(
            style: FilledButton.styleFrom(
              backgroundColor: Colors.red,
            ),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Permanently Delete'),
          ),
        ],
      ),
    );
    if (ok == true) {
      await session.deleteAccount();
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Account and sanctum data successfully erased.'),
            backgroundColor: Colors.red,
          ),
        );
        Navigator.of(context).pushNamedAndRemoveUntil(
          PhoneLoginScreen.route,
          (r) => false,
        );
      }
    }
  }
}

