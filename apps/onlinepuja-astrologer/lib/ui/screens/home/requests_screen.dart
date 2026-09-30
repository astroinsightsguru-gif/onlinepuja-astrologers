import 'dart:async';

import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../../theme/partner_theme.dart';
import '../../widgets/partner_widgets.dart';
import '../call/call_session_screen.dart';
import '../chat/chat_session_screen.dart';

/// Incoming consultation requests for the partner astrologer.
/// Shows rich devotee consultation cards, horoscope quick preview,
/// and instant accept/decline controls.
class RequestsScreen extends StatefulWidget {
  const RequestsScreen({super.key, required this.kind});

  final String kind; // 'call' or 'chat' or 'all'

  @override
  State<RequestsScreen> createState() => _RequestsScreenState();
}

class _RequestsScreenState extends State<RequestsScreen> {
  late Future<List<Map<String, dynamic>>> _future;
  Timer? _poller;
  int _filterIndex = 0; // 0: All, 1: Audio, 2: Video, 3: Chat

  bool get _isCall => widget.kind == 'call';

  @override
  void initState() {
    super.initState();
    _future = _load();
    _poller = Timer.periodic(const Duration(seconds: 4), (_) {
      if (mounted) _reload();
    });
  }

  @override
  void dispose() {
    _poller?.cancel();
    super.dispose();
  }

  int? _activeIncomingAlertId;

  void _checkForIncomingAlert(List<Map<String, dynamic>> list) {
    if (!_isCall || !mounted) return;
    for (final row in list) {
      final id = int.tryParse(
              (row['id'] ?? row['callId'] ?? row['_id']).toString()) ??
          0;
      final status =
          (row['call_status'] ?? row['status'])?.toString() ?? 'Pending';
      if (id > 0 &&
          status.toLowerCase().contains('pending') &&
          _activeIncomingAlertId != id) {
        _activeIncomingAlertId = id;
        final customerName = row['userName']?.toString() ??
            row['customerName']?.toString() ??
            'Customer';
        final avatar =
            row['profile']?.toString() ?? row['userProfile']?.toString();
        final callType =
            (row['call_type'] ?? row['callType'])?.toString() ?? '';
        final isVideo =
            callType == '11' || callType.toLowerCase().contains('video');

        WidgetsBinding.instance.addPostFrameCallback((_) {
          if (!mounted) return;
          IncomingCallDialog.show(
            context,
            callerName: customerName,
            callerAvatar: avatar,
            isVideo: isVideo,
            onAccept: () async {
              Navigator.of(context, rootNavigator: true).pop();
              await _respond(row, accept: true);
            },
            onDecline: () async {
              Navigator.of(context, rootNavigator: true).pop();
              await _respond(row, accept: false);
            },
          );
        });
        break;
      }
    }
  }

  Future<List<Map<String, dynamic>>> _load() async {
    final astrologerId = context.read<PartnerSession>().astrologerId;
    if (astrologerId <= 0) return const [];
    if (widget.kind == 'all') {
      final results = await Future.wait([
        PartnerApi.instance.callRequests(astrologerId: astrologerId).catchError((_) => <Map<String, dynamic>>[]),
        PartnerApi.instance.chatRequests(astrologerId: astrologerId).catchError((_) => <Map<String, dynamic>>[]),
      ]);
      final combined = <Map<String, dynamic>>[...results[0], ...results[1]];
      return combined;
    }
    return _isCall
        ? PartnerApi.instance.callRequests(astrologerId: astrologerId)
        : PartnerApi.instance.chatRequests(astrologerId: astrologerId);
  }

  void _reload() => setState(() => _future = _load());

  Future<void> _respond(
      Map<String, dynamic> row, {required bool accept}) async {
    final isCallRow = _isCall || (row.containsKey('call_type') || row.containsKey('callType'));
    final kindStr = isCallRow ? 'call' : 'chat';
    final id = int.tryParse(
            (row['id'] ?? row['${kindStr}Id'] ?? row['_id']).toString()) ??
        0;
    try {
      if (isCallRow) {
        if (accept) {
          await PartnerApi.instance.acceptCallRequest(id);
        } else {
          await PartnerApi.instance.rejectCallRequest(id);
        }
      } else {
        if (accept) {
          await PartnerApi.instance.acceptChatRequest(id);
        } else {
          await PartnerApi.instance.rejectChatRequest(id);
        }
      }
      if (mounted) {
        showSnack(context, accept ? 'Consultation Accepted' : 'Consultation Declined');
        _reload();
        if (accept) {
          _openSession(row, forceCall: isCallRow);
        }
      }
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    }
  }

  void _openSession(Map<String, dynamic> row, {bool? forceCall}) {
    final isCallSession = forceCall ?? (_isCall || row.containsKey('call_type') || row.containsKey('callType'));
    final customerId = int.tryParse(row['userId']?.toString() ?? '') ?? 0;
    final customerName = row['userName']?.toString() ??
        row['customerName']?.toString() ??
        'Customer';
    final sessionId = (row['id'] ?? row['${isCallSession ? "call" : "chat"}Id'] ?? row['_id'])
        ?.toString();
    final callType = (row['call_type'] ?? row['callType'])?.toString() ?? '';
    final isVideo = callType == '11' || callType.toLowerCase().contains('video');
    if (isCallSession) {
      Navigator.of(context).pushNamed(CallSessionScreen.route, arguments: {
        'customerId': customerId,
        'customerName': customerName,
        'sessionId': sessionId,
        'isVideo': isVideo,
      });
    } else {
      Navigator.of(context).pushNamed(ChatSessionScreen.route, arguments: {
        'customerId': customerId,
        'customerName': customerName,
        'sessionId': sessionId,
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: AppBar(
        title: Text(
          widget.kind == 'all'
              ? 'Consultation Requests'
              : (_isCall ? 'Incoming Calls' : 'Incoming Chats'),
          style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 18),
        ),
      ),
      body: Column(
        children: [
          // Filter Tabs
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 12),
            child: SegmentedPills(
              items: const ['All', 'Audio Call', 'Video Call', 'Chat'],
              selectedIndex: _filterIndex,
              onChanged: (i) => setState(() => _filterIndex = i),
            ),
          ),

          Expanded(
            child: FutureBuilder<List<Map<String, dynamic>>>(
              future: _future,
              builder: (context, snap) {
                if (snap.connectionState == ConnectionState.waiting &&
                    !snap.hasData) {
                  return StatusViews.skeletonList(context, items: 4);
                }
                if (snap.hasError) {
                  return StatusViews.error(context, snap.error!,
                      onRetry: _reload);
                }
                var items = snap.data ?? const <Map<String, dynamic>>[];
                _checkForIncomingAlert(items);

                // Filter logic
                if (_filterIndex == 1) {
                  items = items.where((r) {
                    final ct = (r['call_type'] ?? r['callType'])?.toString() ?? '';
                    return (widget.kind == 'call' || r.containsKey('call_type')) &&
                        ct != '11' &&
                        !ct.toLowerCase().contains('video');
                  }).toList();
                } else if (_filterIndex == 2) {
                  items = items.where((r) {
                    final ct = (r['call_type'] ?? r['callType'])?.toString() ?? '';
                    return ct == '11' || ct.toLowerCase().contains('video');
                  }).toList();
                } else if (_filterIndex == 3) {
                  items = items.where((r) {
                    return !r.containsKey('call_type') && !r.containsKey('callType');
                  }).toList();
                }

                if (items.isEmpty) {
                  return RefreshIndicator(
                    onRefresh: () async => _reload(),
                    child: ListView(
                      padding: const EdgeInsets.symmetric(horizontal: 24),
                      children: [
                        SizedBox(height: MediaQuery.of(context).size.height * 0.15),
                        Center(
                          child: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Container(
                                width: 84,
                                height: 84,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  color: PartnerTheme.saffron
                                      .withValues(alpha: 0.12),
                                ),
                                child: const Icon(
                                  Icons.perm_phone_msg_rounded,
                                  size: 40,
                                  color: PartnerTheme.saffron,
                                ),
                              ),
                              const SizedBox(height: 18),
                              const Text(
                                'No Pending Requests',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.w800,
                                ),
                              ),
                              const SizedBox(height: 6),
                              Text(
                                'Keep your status set to ONLINE. You will automatically receive a full-screen ringing chime when a devotee calls.',
                                textAlign: TextAlign.center,
                                style: TextStyle(
                                  fontSize: 13,
                                  color: dark ? Colors.white60 : Colors.black54,
                                  height: 1.4,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  );
                }

                return RefreshIndicator(
                  onRefresh: () async => _reload(),
                  child: ListView.separated(
                    padding: const EdgeInsets.fromLTRB(16, 4, 16, 24),
                    itemCount: items.length,
                    separatorBuilder: (_, _) => const SizedBox(height: 14),
                    itemBuilder: (context, i) =>
                        _requestCard(context, items[i], dark),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _requestCard(
      BuildContext context, Map<String, dynamic> row, bool dark) {
    final name = row['userName']?.toString() ??
        row['customerName']?.toString() ??
        row['name']?.toString() ??
        'Devotee';
    final callType = (row['call_type'] ?? row['callType'])?.toString() ?? '';
    final isCallRow = _isCall || (row.containsKey('call_type') || row.containsKey('callType'));
    final isVideo = isCallRow && (callType == '11' || callType.toLowerCase().contains('video'));

    return PartnerCard(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header with Devotee Info & Type Pill
          Row(
            children: [
              CircleAvatar(
                radius: 24,
                backgroundColor: isCallRow
                    ? (isVideo ? PartnerTheme.purple.withValues(alpha: 0.2) : PartnerTheme.emerald.withValues(alpha: 0.2))
                    : PartnerTheme.saffron.withValues(alpha: 0.2),
                child: Icon(
                  isCallRow
                      ? (isVideo ? Icons.videocam_rounded : Icons.call_rounded)
                      : Icons.chat_rounded,
                  color: isCallRow
                      ? (isVideo ? PartnerTheme.purple : PartnerTheme.emerald)
                      : PartnerTheme.saffron,
                  size: 24,
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      name,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 8, vertical: 2),
                          decoration: BoxDecoration(
                            color: isCallRow
                                ? (isVideo
                                    ? PartnerTheme.purple.withValues(alpha: 0.15)
                                    : PartnerTheme.emerald.withValues(alpha: 0.15))
                                : PartnerTheme.saffron.withValues(alpha: 0.15),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            isCallRow
                                ? (isVideo ? 'VIDEO CALL' : 'AUDIO CALL')
                                : 'CHAT SESSION',
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.w800,
                              color: isCallRow
                                  ? (isVideo ? PartnerTheme.purple : PartnerTheme.emerald)
                                  : PartnerTheme.saffron,
                            ),
                          ),
                        ),
                        const SizedBox(width: 8),
                        Text(
                          '₹35/min',
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w700,
                            color: PartnerTheme.emerald,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              // View Horoscope / Kundli Button
              IconButton(
                tooltip: 'Devotee Kundli',
                icon: Container(
                  padding: const EdgeInsets.all(6),
                  decoration: BoxDecoration(
                    color: PartnerTheme.amber.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: const Icon(Icons.auto_awesome,
                      color: PartnerTheme.amber, size: 18),
                ),
                onPressed: () => DevoteeKundliSheet.show(
                  context,
                  devoteeName: name,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          const Divider(height: 1),
          const SizedBox(height: 12),

          // Action Buttons: Accept & Start vs Decline
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: () => _respond(row, accept: false),
                  icon: const Icon(Icons.close_rounded, size: 18),
                  label: const Text('Decline'),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: PartnerTheme.crimson,
                    side: BorderSide(
                      color: PartnerTheme.crimson.withValues(alpha: 0.4),
                    ),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                flex: 2,
                child: Container(
                  height: 44,
                  decoration: BoxDecoration(
                    gradient: PartnerTheme.emeraldGradient,
                    borderRadius: BorderRadius.circular(12),
                    boxShadow: PartnerTheme.glow(PartnerTheme.emerald, blur: 8),
                  ),
                  child: Material(
                    color: Colors.transparent,
                    child: InkWell(
                      borderRadius: BorderRadius.circular(12),
                      onTap: () => _respond(row, accept: true),
                      child: const Center(
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.check_circle_rounded,
                                size: 18, color: Colors.white),
                            SizedBox(width: 6),
                            Text(
                              'ACCEPT & START',
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w800,
                                color: Colors.white,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
