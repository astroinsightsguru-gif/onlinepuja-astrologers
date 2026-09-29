import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../auth/login_screen.dart';
import '../call/call_session_screen.dart';
import '../chat/chat_session_screen.dart';
import '../orders/orders_fulfillment_screen.dart';
import '../profile/profile_screen.dart';
import '../wallet/wallet_screen.dart';
import 'availability_screen.dart';
import 'requests_screen.dart';

/// Partner home: bottom tabs for incoming Calls / Chats plus an active background
/// listener that rings with a full-screen/modal dialog whenever a devotee requests
/// a call or chat consultation.
class HomeShell extends StatefulWidget {
  const HomeShell({super.key});

  static const route = '/shell';

  @override
  State<HomeShell> createState() => _HomeShellState();
}

class _HomeShellState extends State<HomeShell> {
  int _tab = 0;
  Timer? _ringerPoller;
  bool _isRinging = false;
  final Set<String> _handledRequests = {};

  @override
  void initState() {
    super.initState();
    // Poll for incoming calls / chats every 4 seconds when astrologer is Online
    _ringerPoller = Timer.periodic(const Duration(seconds: 4), (_) => _pollIncomingConsultations());
  }

  @override
  void dispose() {
    _ringerPoller?.cancel();
    super.dispose();
  }

  Future<void> _pollIncomingConsultations() async {
    if (!mounted || _isRinging) return;
    final session = context.read<PartnerSession>();
    if (!session.isLoggedIn || session.astrologerId <= 0) return;
    if (session.chatStatus != 'Online' && session.callStatus != 'Online') return;

    try {
      // 1. Check incoming calls
      if (session.callStatus == 'Online') {
        final calls = await PartnerApi.instance.callRequests(astrologerId: session.astrologerId);
        for (final c in calls) {
          final id = (c['id'] ?? c['callId'] ?? c['_id'])?.toString() ?? '';
          final status = (c['status'] ?? '').toString().toLowerCase();
          if (id.isNotEmpty && !_handledRequests.contains('call_$id') && (status.isEmpty || status == 'pending' || status == 'waiting')) {
            _handledRequests.add('call_$id');
            _triggerIncomingDialog(c, isCall: true);
            return;
          }
        }
      }

      // 2. Check incoming chats
      if (session.chatStatus == 'Online') {
        final chats = await PartnerApi.instance.chatRequests(astrologerId: session.astrologerId);
        for (final ch in chats) {
          final id = (ch['id'] ?? ch['chatId'] ?? ch['_id'])?.toString() ?? '';
          final status = (ch['status'] ?? '').toString().toLowerCase();
          if (id.isNotEmpty && !_handledRequests.contains('chat_$id') && (status.isEmpty || status == 'pending' || status == 'waiting')) {
            _handledRequests.add('chat_$id');
            _triggerIncomingDialog(ch, isCall: false);
            return;
          }
        }
      }
    } catch (_) {
      // Silent catch for background polling
    }
  }

  void _triggerIncomingDialog(Map<String, dynamic> req, {required bool isCall}) {
    if (!mounted || _isRinging) return;
    setState(() => _isRinging = true);

    // Initial alert vibration & haptic chime
    HapticFeedback.heavyImpact();

    final name = (req['userName'] ?? req['customerName'] ?? req['name'] ?? 'Devotee').toString();
    final callType = (req['call_type'] ?? req['callType'] ?? '').toString();
    final isVideo = isCall && (callType == '11' || callType.toLowerCase().contains('video'));
    final id = int.tryParse((req['id'] ?? req[isCall ? 'callId' : 'chatId'] ?? req['_id'])?.toString() ?? '') ?? 0;
    final customerId = int.tryParse(req['userId']?.toString() ?? '') ?? 0;
    final sessionId = (req['id'] ?? req[isCall ? 'callId' : 'chatId'] ?? req['_id'])?.toString();

    int remainingSeconds = 45;
    Timer? ringTimer;
    Timer? vibrationTimer;

    // Periodic vibration pulses during ringing
    vibrationTimer = Timer.periodic(const Duration(seconds: 2), (_) {
      HapticFeedback.vibrate();
    });

    showModalBottomSheet<bool>(
      context: context,
      isDismissible: false,
      enableDrag: false,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (sheetCtx) => StatefulBuilder(
        builder: (ctx, setModalState) {
          ringTimer ??= Timer.periodic(const Duration(seconds: 1), (t) {
            if (remainingSeconds <= 1) {
              t.cancel();
              vibrationTimer?.cancel();
              if (sheetCtx.mounted) Navigator.pop(sheetCtx, false);
            } else {
              setModalState(() => remainingSeconds--);
            }
          });

          final progress = remainingSeconds / 45.0;

          return Container(
            margin: const EdgeInsets.all(16),
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(28),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.25),
                  blurRadius: 24,
                  offset: const Offset(0, 8),
                ),
              ],
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                // Top Indicator & Ringing Badge
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 5),
                  decoration: BoxDecoration(
                    color: Colors.orange.shade50,
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: Colors.orange.shade300),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const Icon(Icons.ring_volume_rounded, color: Colors.deepOrange, size: 16),
                      const SizedBox(width: 6),
                      Text(
                        'INCOMING ${isCall ? (isVideo ? "VIDEO CALL" : "AUDIO CALL") : "CHAT REQUEST"}',
                        style: const TextStyle(
                          color: Colors.deepOrange,
                          fontWeight: FontWeight.w800,
                          fontSize: 11,
                          letterSpacing: 0.5,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),

                // Pulsing Avatar with Timer Progress Ring
                Stack(
                  alignment: Alignment.center,
                  children: [
                    SizedBox(
                      width: 90,
                      height: 90,
                      child: CircularProgressIndicator(
                        value: progress,
                        strokeWidth: 4,
                        backgroundColor: Colors.grey.shade200,
                        color: Colors.deepOrange,
                      ),
                    ),
                    CircleAvatar(
                      radius: 38,
                      backgroundColor: Colors.deepOrange.shade100,
                      child: Icon(
                        isCall
                            ? (isVideo ? Icons.videocam_rounded : Icons.call_rounded)
                            : Icons.chat_bubble_rounded,
                        color: Colors.deepOrange,
                        size: 36,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // Customer Name
                Text(
                  name,
                  style: const TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.w800,
                    color: Colors.black87,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Waiting for your consultation acceptance... (${remainingSeconds}s)',
                  style: TextStyle(
                    fontSize: 12.5,
                    color: Colors.grey.shade600,
                    fontWeight: FontWeight.w500,
                  ),
                ),
                const SizedBox(height: 24),

                // Action Buttons: Accept (Green) & Decline (Red)
                Row(
                  children: [
                    // Decline Button
                    Expanded(
                      child: OutlinedButton.icon(
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          side: BorderSide(color: Colors.red.shade300),
                          foregroundColor: Colors.red.shade700,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                        icon: const Icon(Icons.call_end_rounded, size: 20),
                        label: const Text('Decline', style: TextStyle(fontWeight: FontWeight.w700)),
                        onPressed: () async {
                          ringTimer?.cancel();
                          vibrationTimer?.cancel();
                          Navigator.pop(sheetCtx, false);
                          try {
                            if (isCall) {
                              await PartnerApi.instance.rejectCallRequest(id);
                            } else {
                              await PartnerApi.instance.rejectChatRequest(id);
                            }
                          } catch (_) {}
                        },
                      ),
                    ),
                    const SizedBox(width: 12),

                    // Accept Button
                    Expanded(
                      flex: 2,
                      child: FilledButton.icon(
                        style: FilledButton.styleFrom(
                          backgroundColor: Colors.green.shade600,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          elevation: 3,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                        icon: const Icon(Icons.check_circle_rounded, size: 22),
                        label: const Text(
                          'ACCEPT & START',
                          style: TextStyle(fontWeight: FontWeight.w800, letterSpacing: 0.5),
                        ),
                        onPressed: () async {
                          ringTimer?.cancel();
                          vibrationTimer?.cancel();
                          Navigator.pop(sheetCtx, true);

                          try {
                            if (isCall) {
                              await PartnerApi.instance.acceptCallRequest(id);
                              if (mounted) {
                                Navigator.of(context).pushNamed(
                                  CallSessionScreen.route,
                                  arguments: {
                                    'customerId': customerId,
                                    'customerName': name,
                                    'sessionId': sessionId,
                                    'isVideo': isVideo,
                                  },
                                );
                              }
                            } else {
                              await PartnerApi.instance.acceptChatRequest(id);
                              if (mounted) {
                                Navigator.of(context).pushNamed(
                                  ChatSessionScreen.route,
                                  arguments: {
                                    'customerId': customerId,
                                    'customerName': name,
                                    'sessionId': sessionId,
                                  },
                                );
                              }
                            }
                          } catch (e) {
                            if (mounted) showSnack(context, 'Accept failed: $e', error: true);
                          }
                        },
                      ),
                    ),
                  ],
                ),
              ],
            ),
          );
        },
      ),
    ).then((accepted) {
      ringTimer?.cancel();
      vibrationTimer?.cancel();
      if (mounted) setState(() => _isRinging = false);
    });
  }

  @override
  Widget build(BuildContext context) {
    final session = context.watch<PartnerSession>();
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(
        title: const Text('Partner Portal', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 18)),
        actions: [
          // Instant Online / Offline Toggle Pill
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 10),
            child: GestureDetector(
              onTap: () async {
                final isOnline = session.chatStatus == 'Online';
                final next = isOnline ? 'Offline' : 'Online';
                await session.setStatus(chat: next, call: next);
                if (context.mounted) {
                  showSnack(
                    context,
                    isOnline
                        ? 'Status set to Offline. Incoming ringing paused.'
                        : 'Status set to Online! Ready to receive calls and chats.',
                  );
                }
              },
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: session.chatStatus == 'Online' ? Colors.green.shade50 : Colors.grey.shade200,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(
                    color: session.chatStatus == 'Online' ? Colors.green : Colors.grey.shade400,
                    width: 1.2,
                  ),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      width: 7.5,
                      height: 7.5,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: session.chatStatus == 'Online' ? Colors.green : Colors.grey.shade600,
                      ),
                    ),
                    const SizedBox(width: 5),
                    Text(
                      session.chatStatus == 'Online' ? 'ONLINE' : 'OFFLINE',
                      style: TextStyle(
                        fontSize: 10.5,
                        fontWeight: FontWeight.w800,
                        color: session.chatStatus == 'Online' ? Colors.green.shade800 : Colors.black87,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
          IconButton(
            tooltip: 'Wallet & Earnings',
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
              leading: const Icon(Icons.assignment_outlined),
              title: const Text('Order Fulfillment'),
              subtitle: const Text('Puja bookings & reports'),
              onTap: () => Navigator.of(context).pushNamed(OrdersFulfillmentScreen.route),
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
