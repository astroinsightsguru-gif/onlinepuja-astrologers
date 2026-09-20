import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../call/call_session_screen.dart';
import '../chat/chat_session_screen.dart';

/// Incoming consultation requests for the partner.
///
/// `kind` is 'call' or 'chat' — rendered from the matching `PartnerApi`
/// request list with accept / reject actions (legacy `MainHomeScreen` tabs).
class RequestsScreen extends StatefulWidget {
  const RequestsScreen({super.key, required this.kind});

  final String kind;

  @override
  State<RequestsScreen> createState() => _RequestsScreenState();
}

class _RequestsScreenState extends State<RequestsScreen> {
  late Future<List<Map<String, dynamic>>> _future;

  bool get _isCall => widget.kind == 'call';

  @override
  void initState() {
    super.initState();
    _future = _load();
  }

  Future<List<Map<String, dynamic>>> _load() async {
    final astrologerId = context.read<PartnerSession>().astrologerId;
    if (astrologerId <= 0) return const [];
    return _isCall
        ? PartnerApi.instance.callRequests(astrologerId: astrologerId)
        : PartnerApi.instance.chatRequests(astrologerId: astrologerId);
  }

  void _reload() => setState(() => _future = _load());

  Future<void> _respond(
      Map<String, dynamic> row, {required bool accept}) async {
    final id = int.tryParse(
            (row['id'] ?? row['${widget.kind}Id'] ?? row['_id']).toString()) ??
        0;
    try {
      if (_isCall) {
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
        showSnack(context, accept ? 'Accepted' : 'Declined');
        _reload();
      }
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    }
  }

  void _openSession(Map<String, dynamic> row) {
    final customerId = int.tryParse(row['userId']?.toString() ?? '') ?? 0;
    final customerName = row['userName']?.toString() ??
        row['customerName']?.toString() ??
        'Customer';
    final sessionId = (row['id'] ?? row['${widget.kind}Id'] ?? row['_id'])
        ?.toString();
    if (_isCall) {
      Navigator.of(context).pushNamed(CallSessionScreen.route, arguments: {
        'customerId': customerId,
        'customerName': customerName,
        'sessionId': sessionId,
        'isVideo': false,
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
    return Scaffold(
      appBar:
          AppBar(title: Text(_isCall ? 'Incoming Calls' : 'Incoming Chats')),
      body: FutureBuilder<List<Map<String, dynamic>>>(
        future: _future,
        builder: (context, snap) {
          if (snap.connectionState == ConnectionState.waiting) {
            return StatusViews.skeletonList(context, items: 4);
          }
          if (snap.hasError) {
            return StatusViews.error(context, snap.error!, onRetry: _reload);
          }
          final items = snap.data ?? const <Map<String, dynamic>>[];
          if (items.isEmpty) {
            return RefreshIndicator(
              onRefresh: () async => _reload(),
              child: ListView(
                children: [
                  SizedBox(
                    height: MediaQuery.of(context).size.height * 0.6,
                    child: StatusViews.empty(
                        context,
                        message: _isCall
                            ? 'No incoming calls right now'
                            : 'No incoming chats right now'),
                  ),
                ],
              ),
            );
          }
          return RefreshIndicator(
            onRefresh: () async => _reload(),
            child: ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: items.length,
              separatorBuilder: (_, _) => const SizedBox(height: 10),
              itemBuilder:
                  (context, i) => _requestCard(context, items[i]),
            ),
          );
        },
      ),
    );
  }

  Widget _requestCard(BuildContext context, Map<String, dynamic> row) {
    final scheme = Theme.of(context).colorScheme;
    final name = row['userName']?.toString() ??
        row['customerName']?.toString() ??
        row['name']?.toString() ??
        'Customer';
    final status = row['status']?.toString() ?? '';
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Row(
          children: [
            CircleAvatar(
              radius: 26,
              backgroundColor: scheme.primaryContainer,
              child: Icon(
                _isCall ? Icons.call_rounded : Icons.chat_rounded,
                color: scheme.primary,
              ),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(name,
                      style: Theme.of(context)
                          .textTheme
                          .titleMedium
                          ?.copyWith(fontWeight: FontWeight.w700)),
                  const SizedBox(height: 4),
                  Text(status.isEmpty
                      ? (_isCall ? 'Audio call request' : 'Chat request')
                      : status,
                      style: Theme.of(context)
                          .textTheme
                          .bodySmall
                          ?.copyWith(color: scheme.outline)),
                ],
              ),
            ),
            IconButton(
              tooltip: 'Open',
              icon: const Icon(Icons.chevron_right_rounded),
              onPressed: () => _openSession(row),
            ),
            IconButton(
              tooltip: 'Accept',
              style:
                  IconButton.styleFrom(backgroundColor: Colors.green.shade100),
              icon: const Icon(Icons.check_rounded, color: Colors.green),
              onPressed: () => _respond(row, accept: true),
            ),
            IconButton(
              tooltip: 'Decline',
              style:
                  IconButton.styleFrom(backgroundColor: scheme.errorContainer),
              icon: Icon(Icons.close_rounded, color: scheme.error),
              onPressed: () => _respond(row, accept: false),
            ),
          ],
        ),
      ),
    );
  }
}

