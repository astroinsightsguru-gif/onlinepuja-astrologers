import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';

/// Toggle chat & call availability (legacy `ChatAvailability` / availability
/// toggle screens). Updates via `astrologer/update`.
class AvailabilityScreen extends StatelessWidget {
  const AvailabilityScreen({super.key});

  static const route = '/availability';

  @override
  Widget build(BuildContext context) {
    final session = context.watch<PartnerSession>();
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(title: const Text('Availability')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Card(
            child: SwitchListTile(
              secondary: Icon(Icons.chat_rounded, color: scheme.primary),
              title: const Text('Available for chat'),
              subtitle: const Text('Receive new chat requests'),
              value: session.chatStatus == 'Online',
              onChanged: (v) => session.setStatus(
                chat: v ? 'Online' : 'Offline',
              ),
            ),
          ),
          const SizedBox(height: 12),
          Card(
            child: SwitchListTile(
              secondary: Icon(Icons.call_rounded, color: scheme.tertiary),
              title: const Text('Available for calls'),
              subtitle: const Text('Receive new call requests'),
              value: session.callStatus == 'Online',
              onChanged: (v) => session.setStatus(
                call: v ? 'Online' : 'Offline',
              ),
            ),
          ),
          const SizedBox(height: 20),
          Text(
            'Keep availability on while you can take sessions. Offline '
            'astrologers do not appear in customer searches.',
            style: Theme.of(context)
                .textTheme
                .bodySmall
                ?.copyWith(color: scheme.outline),
          ),
        ],
      ),
    );
  }
}
