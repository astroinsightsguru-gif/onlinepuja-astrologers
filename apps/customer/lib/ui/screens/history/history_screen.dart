import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';

/// Combined session history (chat + call) from the legacy
/// `getallhistorydetail` contract.
class HistoryScreen extends StatefulWidget {
  const HistoryScreen({super.key});

  @override
  State<HistoryScreen> createState() => _HistoryScreenState();
}

class _HistoryScreenState extends State<HistoryScreen> {
  late Future<List<Map<String, dynamic>>> _future;

  @override
  void initState() {
    super.initState();
    _future = _load();
  }

  Future<List<Map<String, dynamic>>> _load() async {
    final decoded = await ApiClient.instance.post('/getallhistorydetail');
    if (decoded is Map<String, dynamic>) {
      final rl = decoded['recordList'];
      if (rl is List) return rl.whereType<Map<String, dynamic>>().toList();
    }
    return const [];
  }

  void _reload() => setState(() => _future = _load());

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('History')),
      body: RefreshIndicator(
        onRefresh: () async => _reload(),
        child: FutureBuilder<List<Map<String, dynamic>>>(
          future: _future,
          builder: (context, snap) {
            if (snap.connectionState == ConnectionState.waiting) {
              return StatusViews.skeletonList(context, items: 5);
            }
            if (snap.hasError) {
              return ListView(
                children: [
                  SizedBox(
                    height: MediaQuery.of(context).size.height * 0.7,
                    child:
                        StatusViews.error(context, snap.error!, onRetry: _reload),
                  ),
                ],
              );
            }
            final items = snap.data ?? const <Map<String, dynamic>>[];
            if (items.isEmpty) {
              return SizedBox.expand(
                child: StatusViews.empty(
                    context,
                    message: 'No sessions yet — start a chat or call'),
              );
            }
            return ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: items.length,
              separatorBuilder: (_, _) => const SizedBox(height: 10),
              itemBuilder: (context, i) => _row(context, items[i]),
            );
          },
        ),
      ),
    );
  }

  Widget _row(BuildContext context, Map<String, dynamic> row) {
    final scheme = Theme.of(context).colorScheme;
    final type = row['type']?.toString() ?? 'session';
    final name = row['astrologerName']?.toString() ??
        row['name']?.toString() ??
        'Astrologer';
    final amount = double.tryParse(row['amount']?.toString() ?? '') ?? 0;
    final minutes = int.tryParse(row['minutes']?.toString() ?? '') ?? 0;
    final date = row['created_at']?.toString() ?? '';
    final isCall = type.toLowerCase().contains('call');
    return Card(
      child: ListTile(
        leading: CircleAvatar(
          backgroundColor: scheme.primaryContainer,
          child: Icon(
            isCall ? Icons.call_rounded : Icons.chat_rounded,
            color: scheme.primary,
          ),
        ),
        title: Text(name, maxLines: 1, overflow: TextOverflow.ellipsis),
        subtitle: Text(
          [if (date.isNotEmpty) date, if (minutes > 0) '$minutes min']
              .join(' · '),
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
        ),
        trailing: Text(
          '₹${amount.toStringAsFixed(0)}',
          style: Theme.of(context)
              .textTheme
              .titleSmall
              ?.copyWith(fontWeight: FontWeight.w800),
        ),
      ),
    );
  }
}
