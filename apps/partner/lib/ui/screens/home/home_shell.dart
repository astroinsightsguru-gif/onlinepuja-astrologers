import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../auth/login_screen.dart';
import '../profile/profile_screen.dart';
import '../wallet/wallet_screen.dart';
import 'availability_screen.dart';
import 'requests_screen.dart';

/// Partner home: bottom tabs for incoming Calls / Chats plus a drawer with
/// profile, wallet, availability and logout (legacy `MainHomeScreen`).
class HomeShell extends StatefulWidget {
  const HomeShell({super.key});

  static const route = '/shell';

  @override
  State<HomeShell> createState() => _HomeShellState();
}

class _HomeShellState extends State<HomeShell> {
  int _tab = 0;

  @override
  Widget build(BuildContext context) {
    final session = context.watch<PartnerSession>();
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(
        title: Text('Online Puja Astrologer'),
        actions: [
          IconButton(
            tooltip: 'Wallet',
            icon: const Icon(Icons.account_balance_wallet_outlined),
            onPressed: () => Navigator.of(context)
                .pushNamed(WalletScreen.route)
                .then((_) => session.refreshUser()),
          ),
        ],
      ),
      drawer: _drawer(context, session, scheme),
      body: IndexedStack(
        index: _tab,
        children: const [
          RequestsScreen(kind: 'call'),
          RequestsScreen(kind: 'chat'),
        ],
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _tab,
        onDestinationSelected: (i) => setState(() => _tab = i),
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.call_outlined),
            selectedIcon: Icon(Icons.call_rounded),
            label: 'Calls',
          ),
          NavigationDestination(
            icon: Icon(Icons.chat_outlined),
            selectedIcon: Icon(Icons.chat_rounded),
            label: 'Chats',
          ),
        ],
      ),
    );
  }

  Widget _drawer(
      BuildContext context, PartnerSession session, ColorScheme scheme) {
    final u = session.user;
    return Drawer(
      child: SafeArea(
        child: ListView(
          padding: EdgeInsets.zero,
          children: [
            UserAccountsDrawerHeader(
              currentAccountPicture: CircleAvatar(
                backgroundColor: scheme.primaryContainer,
                child: Icon(Icons.auto_awesome, color: scheme.primary),
              ),
              accountName: Text(u?.displayName ?? 'Astrologer'),
              accountEmail: Text(
                '${session.chatStatus} chat · ${session.callStatus} calls',
                style: TextStyle(color: Colors.white.withValues(alpha: 0.9)),
              ),
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [AppTheme.brandSaffron, AppTheme.brandDeep],
                ),
              ),
            ),
            ListTile(
              leading: const Icon(Icons.person_outline_rounded),
              title: const Text('Profile'),
              onTap: () => Navigator.of(context)
                  .pushNamed(ProfileScreen.route)
                  .then((_) => session.refreshUser()),
            ),
            ListTile(
              leading: const Icon(Icons.account_balance_wallet_outlined),
              title: const Text('Wallet'),
              onTap: () => Navigator.of(context)
                  .pushNamed(WalletScreen.route)
                  .then((_) => session.refreshUser()),
            ),
            ListTile(
              leading: const Icon(Icons.toggle_on_outlined),
              title: const Text('Availability'),
              onTap: () => Navigator.of(context).pushNamed(
                  AvailabilityScreen.route),
            ),
            const Divider(),
            ListTile(
              leading: const Icon(Icons.logout_rounded),
              title: const Text('Log out'),
              onTap: () => _logout(context, session),
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _logout(BuildContext context, PartnerSession session) async {
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
      await session.logout();
      if (context.mounted) {
        Navigator.of(context)
            .pushNamedAndRemoveUntil(LoginScreen.route, (r) => false);
      }
    }
  }
}
