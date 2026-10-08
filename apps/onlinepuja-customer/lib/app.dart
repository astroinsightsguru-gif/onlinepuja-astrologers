import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'state/app_session.dart';
import 'ui/screens/astrologer/astrologer_detail_screen.dart';
import 'ui/screens/auth/otp_screen.dart';
import 'ui/screens/auth/phone_login_screen.dart';
import 'ui/screens/call/audio_call_screen.dart';
import 'ui/screens/chat/chat_session_screen.dart';
import 'ui/screens/charity/annadaan_screen.dart';
import 'ui/screens/darshan/live_darshan_screen.dart';
import 'ui/screens/explore/prashna_oracle_screen.dart';
import 'ui/screens/explore/swapna_shastra_screen.dart';
import 'ui/screens/japa/japa_mala_screen.dart';
import 'ui/screens/main_shell.dart';
import 'ui/screens/orders/prasadam_tracker_screen.dart';
import 'ui/screens/profile/wallet_screen.dart';
import 'ui/screens/splash_screen.dart';
import 'ui/screens/notifications_screen.dart';
import 'ui/screens/horoscope/daily_horoscope_screen.dart';
import 'ui/screens/panchang/panchang_screen.dart';
import 'ui/screens/kundli/kundli_list_screen.dart';
import 'ui/screens/kundli/kundli_matching_screen.dart';
import 'ui/screens/puja/puja_list_screen.dart';
import 'ui/screens/mall/mall_screen.dart';
import 'ui/screens/cosmic_ai_screen.dart';
import 'ui/screens/blog/blog_screen.dart';

import 'ui/theme/customer_theme.dart';

class OnlinePujaApp extends StatelessWidget {
  const OnlinePujaApp({super.key});

  @override
  Widget build(BuildContext context) {
    final session = context.watch<AppSession>();
    return MaterialApp(
      title: 'Online Puja',
      debugShowCheckedModeBanner: false,
      theme: CustomerTheme.light(),
      darkTheme: CustomerTheme.dark(),
      themeMode: session.themeMode,
      initialRoute: SplashScreen.route,
      routes: {
        SplashScreen.route: (_) => const SplashScreen(),
        PhoneLoginScreen.route: (_) => const PhoneLoginScreen(),
        OtpScreen.route: (_) => const OtpScreen(),
        MainShell.route: (_) => const MainShell(),
        WalletScreen.route: (_) => const WalletScreen(),
        PrashnaOracleScreen.route: (_) => const PrashnaOracleScreen(),
                LiveDarshanScreen.route: (_) => const LiveDarshanScreen(),
        JapaMalaScreen.route: (_) => const JapaMalaScreen(),
        SwapnaShastraScreen.route: (_) => const SwapnaShastraScreen(),
        AnnadaanScreen.route: (_) => const AnnadaanScreen(),
        NotificationsScreen.route: (_) => const NotificationsScreen(),
        DailyHoroscopeScreen.route: (_) => const DailyHoroscopeScreen(),
        PanchangScreen.route: (_) => const PanchangScreen(),
        KundliListScreen.route: (_) => const KundliListScreen(),
        KundliMatchingScreen.route: (_) => const KundliMatchingScreen(),
        PujaListScreen.route: (_) => const PujaListScreen(),
        MallScreen.route: (_) => const MallScreen(),
        OnlinePujaAiScreen.route: (_) => const OnlinePujaAiScreen(),
        CosmicAiScreen.route: (_) => const OnlinePujaAiScreen(),
        BlogScreen.route: (_) => const BlogScreen(),
      },
      onGenerateRoute: (settings) {
        final args = settings.arguments;
        Map<String, dynamic> map() =>
            args is Map<String, dynamic> ? args : const {};
        switch (settings.name) {
          case AstrologerDetailScreen.route:
            return MaterialPageRoute(
              builder: (_) =>
                  AstrologerDetailScreen(id: map()['astrologerId'] as int),
            );
          case ChatSessionScreen.route:
            return MaterialPageRoute(
              builder: (_) => ChatSessionScreen(
                astrologerId: map()['astrologerId'] as int,
                astrologerName: map()['astrologerName'] as String? ?? 'Astrologer',
                sessionId: map()['sessionId'] as String?,
                isFree: map()['isFree'] as bool? ?? false,
              ),
            );
          case PrasadamTrackerScreen.route:
            return MaterialPageRoute(
              builder: (_) => PrasadamTrackerScreen(
                orderId: map()['orderId'] as String?,
                pujaName: map()['pujaName'] as String?,
                temple: map()['temple'] as String?,
                trackingCode: map()['trackingCode'] as String?,
                courierName: map()['courierName'] as String?,
                currentStep: (map()['currentStep'] as int?) ?? 3,
              ),
            );
          case AudioCallScreen.route:
            return MaterialPageRoute(
              builder: (_) => AudioCallScreen(
                astrologerId: map()['astrologerId'] as int,
                astrologerName:
                    map()['astrologerName'] as String? ?? 'Astrologer',
                sessionId: map()['sessionId'] as String?,
                isVideo: map()['isVideo'] as bool? ?? false,
              ),
            );
        }
        return null;
      },
    );
  }
}

/// Convenience navigation helpers.
extension OpNav on BuildContext {
  Future<void> openAstrologer(int id) => pushNamed(
      AstrologerDetailScreen.route, {'astrologerId': id});

  Future<void> openChat({
    required int astrologerId,
    required String astrologerName,
    String? sessionId,
    bool isFree = false,
  }) =>
      pushNamed(ChatSessionScreen.route, {
        'astrologerId': astrologerId,
        'astrologerName': astrologerName,
        'sessionId': sessionId,
        'isFree': isFree,
      });

  Future<void> openCall({
    required int astrologerId,
    required String astrologerName,
    double ratePerMinute = 15.0,
    String? sessionId,
    bool isVideo = false,
  }) =>
      pushNamed(AudioCallScreen.route, {
        'astrologerId': astrologerId,
        'astrologerName': astrologerName,
        'ratePerMinute': ratePerMinute,
        'sessionId': sessionId,
        'isVideo': isVideo,
      });

  Future<void> openPrashnaOracle() => pushNamed(PrashnaOracleScreen.route);

  Future<void> openPrasadamTracker({
    String? orderId,
    String? pujaName,
    String? temple,
    String? trackingCode,
    String? courierName,
    int currentStep = 3,
  }) =>
      pushNamed(PrasadamTrackerScreen.route, {
        'orderId': orderId,
        'pujaName': pujaName,
        'temple': temple,
        'trackingCode': trackingCode,
        'courierName': courierName,
        'currentStep': currentStep,
      });

  Future<void> openLiveDarshan() => pushNamed(LiveDarshanScreen.route);

  Future<void> openJapaMala() => pushNamed(JapaMalaScreen.route);

  Future<void> openSwapnaShastra() => pushNamed(SwapnaShastraScreen.route);

  Future<void> openAnnadaan() => pushNamed(AnnadaanScreen.route);

  Future<void> pushNamed(String route, [Map<String, dynamic>? args]) =>
      Navigator.of(this).pushNamed(route, arguments: args);
}
