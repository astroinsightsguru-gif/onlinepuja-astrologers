import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';

/// Notification centre (legacy `notificationScreen.dart`). In-app alerts via
/// `getUserNotification`, delete one/all via the userNotification endpoints.
/// FCM push lands here too once Firebase is wired on the device side.
class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({super.key});

  static const route = '/notifications';

  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  List<Map<String, dynamic>>? _items;
  bool _clearing = false;
  Object? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final userId = context.read<AppSession>().user?.id ?? 0;
    try {
      final items = await MiscApi.instance.notifications(userId: userId);
      if (mounted) setState(() => _items = items);
    } catch (e) {
      if (mounted) setState(() => _error = e);
    }
  }

  static String _title(Map<String, dynamic> n) =>
      (n['title'] ?? n['notificationTitle'] ?? 'Notification').toString();

  static String _body(Map<String, dynamic> n) =>
      (n['description'] ?? n['message'] ?? n['body'] ?? '').toString();

  static DateTime? _when(Map<String, dynamic> n) {
    for (final k in ['createdAt', 'created_at', 'datetime', 'date']) {
      final d = DateTime.tryParse(n[k]?.toString() ?? '');
      if (d != null) return d;
    }
    return null;
  }

  Future<void> _deleteAll() async {
    final userId = context.read<AppSession>().user?.id ?? 0;
    setState(() => _clearing = true);
    try {
      await MiscApi.instance.deleteAllNotifications(userId: userId);
      if (mounted) setState(() => _items = const []);
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    } finally {
      if (mounted) setState(() => _clearing = false);
    }
  }

  Future<void> _deleteOne(Map<String, dynamic> n) async {
    try {
      await MiscApi.instance
          .deleteNotification(notificationId: n['id'] ?? n['notificationId']);
      if (mounted) {
        setState(() => _items = [...?_items]..remove(n));
      }
    } on ApiException catch (e) {
      if (mounted) showSnack(context, e.message, error: true);
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(
        title: const Text('Notifications'),
        actions: [
          if (_items != null && _items!.isNotEmpty)
            TextButton(
              onPressed: _clearing ? null : _deleteAll,
              child: Text('Clear all',
                  style: TextStyle(color: scheme.error)),
            ),
        ],
      ),
      body: _error != null
          ? StatusViews.error(context, _error!, onRetry: _load)
          : _items == null
              ? const Center(child: CircularProgressIndicator())
              : _items!.isEmpty
                  ? StatusViews.empty(context,
                      message: 'No notifications yet.\nAlerts & offers land here.')
                  : RefreshIndicator(
                      onRefresh: _load,
                      child: ListView.separated(
                        padding: const EdgeInsets.all(12),
                        itemCount: _items!.length,
                        separatorBuilder: (_, _) => const SizedBox(height: 8),
                        itemBuilder: (context, i) {
                          final n = _items![i];
                          final when = _when(n);
                          return Dismissible(
                            key: ValueKey(n['id'] ?? i),
                            direction: DismissDirection.endToStart,
                            onDismissed: (_) => _deleteOne(n),
                            background: Container(
                              alignment: Alignment.centerRight,
                              padding: const EdgeInsets.only(right: 20),
                              decoration: BoxDecoration(
                                color: scheme.errorContainer,
                                borderRadius: BorderRadius.circular(14),
                              ),
                              child: Icon(Icons.delete_outline,
                                  color: scheme.error),
                            ),
                            child: Card(
                              child: ListTile(
                                leading: CircleAvatar(
                                  backgroundColor: scheme.primaryContainer,
                                  child: Icon(Icons.notifications_rounded,
                                      color: scheme.primary, size: 20),
                                ),
                                title: Text(_title(n),
                                    style: const TextStyle(
                                        fontWeight: FontWeight.w700)),
                                subtitle: Text(_body(n),
                                    maxLines: 3,
                                    overflow: TextOverflow.ellipsis),
                                trailing: when == null
                                    ? null
                                    : Text(
                                        '${when.day}/${when.month}',
                                        style: Theme.of(context)
                                            .textTheme
                                            .bodySmall
                                            ?.copyWith(
                                                color: scheme.outline),
                                      ),
                              ),
                            ),
                          );
                        },
                      ),
                    ),
    );
  }
}
