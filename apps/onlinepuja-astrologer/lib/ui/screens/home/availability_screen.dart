import 'package:flutter/material.dart';
import 'package:op_shared/op_shared.dart';
import 'package:provider/provider.dart';

import '../../../state/app_session.dart';
import '../../theme/partner_theme.dart';
import '../../widgets/partner_widgets.dart';

/// Comprehensive Availability & Schedule Manager for Astrologers.
/// Controls master online/offline status, granular session types,
/// break presets, and schedule guidelines.
class AvailabilityScreen extends StatefulWidget {
  const AvailabilityScreen({super.key});

  static const route = '/availability';

  @override
  State<AvailabilityScreen> createState() => _AvailabilityScreenState();
}

class _AvailabilityScreenState extends State<AvailabilityScreen> {
  int? _selectedBreakMinutes;

  @override
  Widget build(BuildContext context) {
    final session = context.watch<PartnerSession>();
    final dark = Theme.of(context).brightness == Brightness.dark;
    final isOnline = session.chatStatus == 'Online';

    return Scaffold(
      appBar: AppBar(
        title: Text(AppStrings.availabilityHours,
            style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 18)),
      ),
      body: ListView(
        padding: const EdgeInsets.fromLTRB(16, 12, 16, 28),
        children: [
          // Master Status Banner
          PartnerCard(
            gradient: isOnline
                ? PartnerTheme.emeraldGradient
                : (dark
                    ? PartnerTheme.darkCardGradient
                    : const LinearGradient(
                        colors: [Color(0xFFE5E7EB), Color(0xFFD1D5DB)])),
            padding: const EdgeInsets.all(20),
            child: Row(
              children: [
                Container(
                  width: 54,
                  height: 54,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: Colors.white.withValues(alpha: 0.2),
                  ),
                  child: Icon(
                    isOnline ? Icons.sensors_rounded : Icons.bedtime_rounded,
                    color: Colors.white,
                    size: 28,
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        isOnline ? AppStrings.portalIsLive : AppStrings.currentlyOfflineUpper,
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.w900,
                          color: Colors.white,
                        ),
                      ),
                      const SizedBox(height: 3),
                      Text(
                        isOnline
                            ? 'You are visible to devotees and will receive consultation calls.'
                            : 'You are invisible in devotee searches. Turn on to accept sessions.',
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.white.withValues(alpha: 0.9),
                        ),
                      ),
                    ],
                  ),
                ),
                Switch(
                  value: isOnline,
                  activeThumbColor: Colors.white,
                  activeTrackColor: PartnerTheme.emeraldDark,
                  onChanged: (v) {
                    session.setStatus(
                      chat: v ? 'Online' : 'Offline',
                      call: v ? 'Online' : 'Offline',
                    );
                    showSnack(
                      context,
                      v
                          ? 'You are now Online and accepting consultations!'
                          : 'You are now Offline. Incoming alerts paused.',
                    );
                  },
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),

          // Granular Consultation Switches
          Text(
            AppStrings.consultationModes,
            style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
          ),
          const SizedBox(height: 10),
          PartnerCard(
            padding: EdgeInsets.zero,
            child: Column(
              children: [
                _serviceTile(
                  icon: Icons.chat_rounded,
                  color: PartnerTheme.saffron,
                  title: 'Chat Consultations',
                  subtitle: 'Receive text queries & instant horoscopes',
                  active: session.chatStatus == 'Online',
                  onChanged: (v) => session.setStatus(
                    chat: v ? 'Online' : 'Offline',
                  ),
                ),
                const Divider(height: 1, indent: 64),
                _serviceTile(
                  icon: Icons.call_rounded,
                  color: PartnerTheme.emerald,
                  title: 'Audio Phone Calls',
                  subtitle: 'Direct high-fidelity voice consultations',
                  active: session.callStatus == 'Online',
                  onChanged: (v) => session.setStatus(
                    call: v ? 'Online' : 'Offline',
                  ),
                ),
                const Divider(height: 1, indent: 64),
                _serviceTile(
                  icon: Icons.videocam_rounded,
                  color: PartnerTheme.purple,
                  title: 'Video Face-to-Face Calls',
                  subtitle: 'Face-to-face Kundli reading & palmistry',
                  active: session.callStatus == 'Online',
                  onChanged: (v) => session.setStatus(
                    call: v ? 'Online' : 'Offline',
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),

          // Quick Break Presets (DND)
          Text(
            AppStrings.takeQuickBreak,
            style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
          ),
          const SizedBox(height: 6),
          Text(
            'Temporarily pause incoming alerts. Auto-resumes after break duration.',
            style: TextStyle(
              fontSize: 12,
              color: dark ? Colors.white60 : Colors.black54,
            ),
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              _breakChip(15, AppStrings.break15Min, Icons.coffee_rounded, session),
              const SizedBox(width: 8),
              _breakChip(30, AppStrings.break30Min, Icons.self_improvement_rounded, session),
              const SizedBox(width: 8),
              _breakChip(60, AppStrings.break60Min, Icons.bedtime_outlined, session),
            ],
          ),
          const SizedBox(height: 22),

          // Working Hours Info
          PartnerCard(
            padding: const EdgeInsets.all(16),
            borderColor: PartnerTheme.gold.withValues(alpha: 0.4),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: PartnerTheme.gold.withValues(alpha: 0.15),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: const Icon(Icons.stars_rounded,
                          color: PartnerTheme.gold, size: 20),
                    ),
                    const SizedBox(width: 10),
                    const Text(
                      'Acharya Ranking & Algorithm Tips',
                      style:
                          TextStyle(fontSize: 14, fontWeight: FontWeight.w800),
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                Text(
                  '• Astrologers who stay online for 4+ hours daily appear on the customer app Top Banner.\n'
                  '• Maintaining a 95%+ call answer rate unlocks "Verified Featured Acharya" badge.\n'
                  '• Evening hours (07:00 PM – 11:30 PM) have the highest devotee consultation demand.',
                  style: TextStyle(
                    fontSize: 12.5,
                    height: 1.5,
                    color: dark ? Colors.white70 : const Color(0xFF4B5563),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _serviceTile({
    required IconData icon,
    required Color color,
    required String title,
    required String subtitle,
    required bool active,
    required ValueChanged<bool> onChanged,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.15),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(icon, color: color, size: 22),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 14.5,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  subtitle,
                  style: TextStyle(
                    fontSize: 11.5,
                    color: Colors.grey.shade600,
                  ),
                ),
              ],
            ),
          ),
          Switch(
            value: active,
            activeThumbColor: color,
            onChanged: onChanged,
          ),
        ],
      ),
    );
  }

  Widget _breakChip(int minutes, String label, IconData icon, PartnerSession session) {
    final isSelected = _selectedBreakMinutes == minutes;
    return Expanded(
      child: GestureDetector(
        onTap: () {
          setState(() {
            if (isSelected) {
              _selectedBreakMinutes = null;
              session.setStatus(chat: 'Online', call: 'Online');
              showSnack(context, 'Break cancelled. You are Online.');
            } else {
              _selectedBreakMinutes = minutes;
              session.setStatus(chat: 'Offline', call: 'Offline');
              showSnack(context, '$minutes min break started. Status set to Offline.');
            }
          });
        },
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: isSelected
                ? PartnerTheme.saffron.withValues(alpha: 0.15)
                : (Theme.of(context).brightness == Brightness.dark
                    ? PartnerTheme.darkCard
                    : const Color(0xFFF7F5EE)),
            borderRadius: BorderRadius.circular(14),
            border: Border.all(
              color: isSelected
                  ? PartnerTheme.saffron
                  : (Theme.of(context).brightness == Brightness.dark
                      ? PartnerTheme.darkBorder
                      : const Color(0xFFE5DDD0)),
              width: isSelected ? 1.8 : 1,
            ),
          ),
          child: Column(
            children: [
              Icon(
                icon,
                size: 20,
                color: isSelected ? PartnerTheme.saffron : Colors.grey,
              ),
              const SizedBox(height: 6),
              Text(
                label,
                style: TextStyle(
                  fontSize: 11,
                  fontWeight: isSelected ? FontWeight.w800 : FontWeight.w600,
                  color: isSelected ? PartnerTheme.saffron : null,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
