import 'package:flutter_test/flutter_test.dart';
import 'package:op_shared/op_shared.dart';

void main() {
  group('Shared Models & Env Test', () {
    test('Env constants are configured for production', () {
      expect(Env.apiBase, 'https://onlinepuja.live/api');
      expect(Env.imageBase, 'https://onlinepuja.live/');
      expect(Env.websiteUrl, 'https://onlinepuja.live/');
    });

    test('Astrologer model deserialization and imageUrl getter', () {
      final json = {
        'id': 101,
        'name': 'Acharya Sharma',
        'email': 'sharma@example.com',
        'contactNo': '9876543210',
        'gender': 'Male',
        'experienceInYears': 15,
        'charge': 25,
        'videoCallRate': 40,
        'isFreeAvailable': true,
        'rating': 4.9,
        'allSkill': 'Vedic, Vastu',
        'primarySkill': 'Vedic Astrology',
        'languageKnown': 'Hindi, English, Sanskrit',
        'profileImage': 'uploads/astrologer/sharma.jpg',
      };

      final astro = Astrologer.fromJson(json);
      expect(astro.id, 101);
      expect(astro.name, 'Acharya Sharma');
      expect(astro.experienceInYears, 15);
      expect(astro.charge, 25.0);
      expect(astro.videoCallRate, 40.0);
      expect(astro.rating, 4.9);
      expect(astro.isFreeAvailable, true);
      expect(astro.imageUrl, 'https://onlinepuja.live/uploads/astrologer/sharma.jpg');
    });

    test('Puja model deserialization', () {
      final json = {
        'id': 12,
        'pujaTitle': 'Griha Pravesh Puja',
        'subtitle': 'Auspicious Vedic Housewarming Ceremony',
        'place': 'Home Altar',
      };

      final puja = Puja.fromJson(json);
      expect(puja.id, 12);
      expect(puja.title, 'Griha Pravesh Puja');
      expect(puja.subtitle, 'Auspicious Vedic Housewarming Ceremony');
      expect(puja.place, 'Home Altar');
    });

    test('Product model deserialization', () {
      final json = {
        'id': 34,
        'name': 'Natural Blue Sapphire (Neelam)',
        'price': 15000,
        'description': '100% Certified Vedic Gemstone',
      };

      final product = Product.fromJson(json);
      expect(product.id, 34);
      expect(product.name, 'Natural Blue Sapphire (Neelam)');
      expect(product.price, 15000);
    });
  });
}
