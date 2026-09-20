import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../auth/phone_login_screen.dart';
import '../notifications_screen.dart';
import 'wallet_screen.dart';

/// Profile tab: user info, wallet shortcut and account actions.
class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final session = context.watch<AppSession>();
    final u = session.user;
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(title: const Text('Profile')),
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
            onTap: () {},
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
            onTap: () {},
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
