import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

/// Full-screen or modal incoming call ringing interface.
///
/// Features animated pulsating rings around the caller avatar,
/// clear consult type indicator (Audio/Video), and Accept / Decline actions.
class IncomingCallDialog extends StatefulWidget {
  const IncomingCallDialog({
    super.key,
    required this.callerName,
    this.callerAvatar,
    this.isVideo = false,
    this.ratePerMinute,
    required this.onAccept,
    required this.onDecline,
  });

  final String callerName;
  final String? callerAvatar;
  final bool isVideo;
  final String? ratePerMinute;
  final Future<void> Function() onAccept;
  final Future<void> Function() onDecline;

  static Future<bool?> show(
    BuildContext context, {
    required String callerName,
    String? callerAvatar,
    bool isVideo = false,
    String? ratePerMinute,
    required Future<void> Function() onAccept,
    required Future<void> Function() onDecline,
  }) {
    return showDialog<bool>(
      context: context,
      barrierDismissible: false,
      builder: (_) => IncomingCallDialog(
        callerName: callerName,
        callerAvatar: callerAvatar,
        isVideo: isVideo,
        ratePerMinute: ratePerMinute,
        onAccept: onAccept,
        onDecline: onDecline,
      ),
    );
  }

  @override
  State<IncomingCallDialog> createState() => _IncomingCallDialogState();
}

class _IncomingCallDialogState extends State<IncomingCallDialog>
    with SingleTickerProviderStateMixin {
  late AnimationController _pulseController;
  bool _acting = false;

  @override
  void initState() {
    super.initState();
    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat();
  }

  @override
  void dispose() {
    _pulseController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Dialog(
      insetPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 40),
      backgroundColor: Colors.transparent,
      elevation: 0,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 36),
        decoration: BoxDecoration(
          color: AppTheme.brandDeep,
          borderRadius: BorderRadius.circular(28),
          border: Border.all(color: Colors.white24, width: 1.5),
          boxShadow: const [
            BoxShadow(color: Colors.black54, blurRadius: 24, spreadRadius: 4),
          ],
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
              decoration: BoxDecoration(
                color: Colors.white.withValues(alpha: 0.12),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(
                    widget.isVideo ? Icons.videocam_rounded : Icons.call_rounded,
                    color: AppTheme.brandSaffron,
                    size: 16,
                  ),
                  const SizedBox(width: 8),
                  Text(
                    widget.isVideo ? 'Incoming Video Call…' : 'Incoming Audio Call…',
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 32),

            // Pulsing Avatar
            AnimatedBuilder(
              animation: _pulseController,
              builder: (ctx, child) {
                return Stack(
                  alignment: Alignment.center,
                  children: [
                    Container(
                      width: 130 + (_pulseController.value * 25),
                      height: 130 + (_pulseController.value * 25),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: AppTheme.brandSaffron
                            .withValues(alpha: (1.0 - _pulseController.value) * 0.35),
                      ),
                    ),
                    Container(
                      width: 110,
                      height: 110,
                      decoration: const BoxDecoration(
                        shape: BoxShape.circle,
                        color: Colors.white12,
                      ),
                      child: const CircleAvatar(
                        radius: 50,
                        backgroundColor: AppTheme.brandSaffron,
                        child: Icon(Icons.person, color: Colors.white, size: 54),
                      ),
                    ),
                  ],
                );
              },
            ),
            const SizedBox(height: 24),

            Text(
              widget.callerName,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 22,
                fontWeight: FontWeight.bold,
              ),
            ),
            if (widget.ratePerMinute != null) ...[
              const SizedBox(height: 6),
              Text(
                'Rate: ₹${widget.ratePerMinute}/min',
                style: const TextStyle(color: Colors.amberAccent, fontSize: 14),
              ),
            ],
            const SizedBox(height: 36),

            // Action Buttons
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                // Decline Button
                Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    IconButton(
                      style: IconButton.styleFrom(
                        backgroundColor: Colors.redAccent,
                        padding: const EdgeInsets.all(18),
                      ),
                      onPressed: _acting
                          ? null
                          : () async {
                              setState(() => _acting = true);
                              try {
                                await widget.onDecline();
                              } finally {
                                if (context.mounted) Navigator.pop(context, false);
                              }
                            },
                      icon: const Icon(Icons.call_end_rounded, color: Colors.white, size: 30),
                    ),
                    const SizedBox(height: 8),
                    const Text('Decline', style: TextStyle(color: Colors.white70, fontSize: 13)),
                  ],
                ),

                // Accept Button
                Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    IconButton(
                      style: IconButton.styleFrom(
                        backgroundColor: Colors.green,
                        padding: const EdgeInsets.all(18),
                      ),
                      onPressed: _acting
                          ? null
                          : () async {
                              setState(() => _acting = true);
                              try {
                                await widget.onAccept();
                              } finally {
                                if (context.mounted) Navigator.pop(context, true);
                              }
                            },
                      icon: Icon(
                        widget.isVideo ? Icons.videocam_rounded : Icons.call_rounded,
                        color: Colors.white,
                        size: 30,
                      ),
                    ),
                    const SizedBox(height: 8),
                    const Text('Accept', style: TextStyle(color: Colors.greenAccent, fontSize: 13, fontWeight: FontWeight.bold)),
                  ],
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
