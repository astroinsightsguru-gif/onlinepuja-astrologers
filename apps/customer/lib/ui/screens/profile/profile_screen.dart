import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../state/app_session.dart';
import '../auth/phone_login_screen.dart';
import '../history/history_screen.dart';
import '../notifications_screen.dart';
import 'wallet_screen.dart';

/// Profile tab: user info, wallet shortcut, appearance, and account actions.
class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final session = context.watch<AppSession>();
    final u = session.user;
    final scheme = Theme.of(context).colorScheme;
    final currentTheme = session.themeMode;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Profile'),
        actions: [
          IconButton(
            tooltip: 'Toggle Theme',
            icon: Icon(
              currentTheme == ThemeMode.light
                  ? Icons.light_mode_rounded
                  : currentTheme == ThemeMode.dark
                      ? Icons.dark_mode_rounded
                      : Icons.brightness_auto_rounded,
            ),
            onPressed: () {
              final next = currentTheme == ThemeMode.dark
                  ? ThemeMode.light
                  : currentTheme == ThemeMode.light
                      ? ThemeMode.system
                      : ThemeMode.dark;
              session.setThemeMode(next);
            },
          ),
        ],
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _header(context, u, scheme),
          const SizedBox(height: 20),
          _tile(
            context,
            icon: Icons.account_balance_wallet_outlined,
            title: 'Wallet',
            subtitle: u == null
                ? 'Recharge to get started'
                : '${session.flags.currency}${u.walletAmount.toStringAsFixed(0)}',
            onTap: () =>
                Navigator.of(context).pushNamed(WalletScreen.route),
          ),
          _tile(
            context,
            icon: Icons.history_rounded,
            title: 'History',
            subtitle: 'Your chats & calls',
            onTap: () => Navigator.of(context).push(
                MaterialPageRoute(builder: (_) => const HistoryScreen())),
          ),
          _tile(
            context,
            icon: Icons.notifications_outlined,
            title: 'Notifications',
            subtitle: 'Alerts & offers',
            onTap: () => Navigator.of(context).push(MaterialPageRoute(
                builder: (_) => const NotificationsScreen())),
          ),
          _tile(
            context,
            icon: Icons.headset_mic_outlined,
            title: 'Support',
            subtitle: 'Help & feedback',
            onTap: () => _showSupport(context),
          ),
          const SizedBox(height: 16),
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(Icons.palette_outlined,
                          size: 20, color: scheme.primary),
                      const SizedBox(width: 8),
                      Text(
                        'Appearance',
                        style: Theme.of(context)
                            .textTheme
                            .titleSmall
                            ?.copyWith(fontWeight: FontWeight.w700),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  SegmentedButton<ThemeMode>(
                    segments: const [
                      ButtonSegment(
                        value: ThemeMode.light,
                        icon: Icon(Icons.light_mode_rounded),
                        label: Text('Lite'),
                      ),
                      ButtonSegment(
                        value: ThemeMode.dark,
                        icon: Icon(Icons.dark_mode_rounded),
                        label: Text('Dark'),
                      ),
                      ButtonSegment(
                        value: ThemeMode.system,
                        icon: Icon(Icons.brightness_auto_rounded),
                        label: Text('System'),
                      ),
                    ],
                    selected: {session.themeMode},
                    onSelectionChanged: (set) {
                      if (set.isNotEmpty) session.setThemeMode(set.first);
                    },
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 20),
          OutlinedButton.icon(
            onPressed: session.isLoggedIn ? () => _logout(context) : null,
            icon: const Icon(Icons.logout_rounded),
            label: const Text('Log out'),
          ),
        ],
      ),
    );
  }

  void _showSupport(BuildContext context) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text(
                'Help & Customer Support',
                style: Theme.of(ctx).textTheme.titleLarge?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
              ),
              const SizedBox(height: 12),
              const Text(
                'Have questions regarding pujas, consultations, or wallet recharge? We are here 24/7 to assist you.',
              ),
              const SizedBox(height: 20),
              ListTile(
                leading: const CircleAvatar(
                  backgroundColor: Color(0xFF25D366),
                  child: Icon(Icons.chat_bubble_outline, color: Colors.white),
                ),
                title: const Text('WhatsApp Support'),
                subtitle: const Text('+91 9305932724'),
                onTap: () async {
                  Navigator.of(ctx).pop();
                  final uri = Uri.parse('https://wa.me/919305932724?text=Hello%20OnlinePuja%20Support%2C%20I%20need%20assistance.');
                  try {
                    await launchUrl(uri, mode: LaunchMode.externalApplication);
                  } catch (_) {}
                },
              ),
              ListTile(
                leading: CircleAvatar(
                  backgroundColor: Theme.of(ctx).colorScheme.primaryContainer,
                  child: Icon(Icons.email_outlined,
                      color: Theme.of(ctx).colorScheme.primary),
                ),
                title: const Text('Email Us'),
                subtitle: const Text('support@onlinepuja.live'),
                onTap: () async {
                  Navigator.of(ctx).pop();
                  final uri = Uri.parse('mailto:support@onlinepuja.live?subject=Customer%20Support%20Inquiry');
                  try {
                    await launchUrl(uri, mode: LaunchMode.externalApplication);
                  } catch (_) {}
                },
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _header(
      BuildContext context, User? u, ColorScheme scheme) {
    return Row(
      children: [
        CircleAvatar(
          radius: 34,
          backgroundColor: scheme.primaryContainer,
          child: Icon(Icons.person_rounded, size: 36, color: scheme.primary),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                u?.displayName ?? 'Guest',
                style: Theme.of(context)
                    .textTheme
                    .titleLarge
                    ?.copyWith(fontWeight: FontWeight.w800),
              ),
              const SizedBox(height: 4),
              Text(
                u?.contactNo == null || (u?.contactNo?.isEmpty ?? true)
                    ? 'Sign in to sync your profile'
                    : '${u?.countryCode ?? ''} ${u?.contactNo ?? ''}',
                style: Theme.of(context)
                    .textTheme
                    .bodyMedium
                    ?.copyWith(color: scheme.outline),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _tile(
    BuildContext context, {
    required IconData icon,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    final scheme = Theme.of(context).colorScheme;
    return Card(
      child: ListTile(
        leading: CircleAvatar(
          backgroundColor: scheme.primaryContainer,
          child: Icon(icon, color: scheme.primary),
        ),
        title: Text(title),
        subtitle: Text(subtitle),
        trailing: const Icon(Icons.chevron_right_rounded),
        onTap: onTap,
      ),
    );
  }

  Future<void> _logout(BuildContext context) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Log out?'),
        content: const Text('You can sign back in anytime with your mobile number.'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(false),
            child: const Text('Cancel'),
          ),
          FilledButton(
            onPressed: () => Navigator.of(ctx).pop(true),
            child: const Text('Log out'),
          ),
        ],
      ),
    );
    if (confirmed == true && context.mounted) {
      await context.read<AppSession>().logout();
      if (context.mounted) {
        Navigator.of(context)
            .pushNamedAndRemoveUntil(PhoneLoginScreen.route, (r) => false);
      }
    }
  }
}
