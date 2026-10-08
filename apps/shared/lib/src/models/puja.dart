/// Puja (worship booking) models mirroring the legacy
/// `pujaModel.dart` / `PujaCategoriesListModel.dart` /
/// `RecommendedPujaListModel.dart` contracts.
library;

import 'dart:convert';

/// Category from `/getPujaCategory`.
class PujaCategory {
  PujaCategory({this.id, this.name, this.image, this.isActive});

  dynamic id, name, image, isActive;

  factory PujaCategory.fromJson(Map<String, dynamic> json) => PujaCategory(
        id: json['id'],
        name: json['name'],
        image: json['image'],
        isActive: json['isActive'],
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'image': image,
        'isActive': isActive,
      };
}

/// A puja from `/getPujaList` / `/getPujaRecommend`.
class Puja {
  Puja({
    this.id,
    this.categoryId,
    this.title,
    this.slug,
    this.subtitle,
    this.place,
    this.longDescription,
    this.benefits,
    this.images,
    this.startDatetime,
    this.endDatetime,
    this.packageIds,
    this.status,
    this.packages,
    this.isPurchased,
  });

  dynamic id, categoryId, title, slug, subtitle, place, longDescription;
  List<String>? benefits, images;
  dynamic startDatetime, endDatetime;
  List<dynamic>? packageIds;
  dynamic status;
  List<PujaPackage>? packages;
  dynamic isPurchased;

  factory Puja.fromJson(Map<String, dynamic> json) {
    final rawBenefits = json['puja_benefits'] ?? json['pujaBenefits'];
    final rawImages = json['puja_images'] ?? json['pujaImages'];
    final rawPackages = json['packages'];

    return Puja(
      id: json['id'],
      categoryId: json['category_id'] ?? json['categoryId'],
      title: (json['puja_title'] ?? json['pujaTitle'] ?? json['title'] ?? json['name'] ?? '').toString(),
      slug: json['slug']?.toString(),
      subtitle: (json['puja_subtitle'] ?? json['pujaSubtitle'] ?? json['subtitle'] ?? '').toString(),
      place: (json['puja_place'] ?? json['pujaPlace'] ?? json['place'] ?? '').toString(),
      longDescription:
          (json['long_description'] ?? json['longDescription'] ?? json['description'] ?? '').toString(),
      benefits: rawBenefits is List
          ? List<String>.from(
              rawBenefits.map((x) => x is Map
                  ? (x['benefit'] ?? x['title'] ?? x['description'] ?? '').toString()
                  : x.toString()))
          : const <String>[],
      images: rawImages is List
          ? List<String>.from(rawImages.map((x) => x.toString()))
          : const <String>[],
      startDatetime:
          json['puja_start_datetime'] ?? json['pujaStartDatetime'] ?? json['startDatetime'],
      endDatetime: json['puja_end_datetime'] ?? json['pujaEndDatetime'] ?? json['endDatetime'],
      packageIds: (json['package_id'] ?? json['packageId']) is List
          ? List<dynamic>.from(json['package_id'] ?? json['packageId'])
          : const <dynamic>[],
      status: json['puja_status'] ?? json['pujaStatus'] ?? json['status'],
      packages: rawPackages is List
          ? List<PujaPackage>.from(
              rawPackages.map((x) => PujaPackage.fromJson(
                  x is Map<String, dynamic> ? x : Map<String, dynamic>.from(x as Map))))
          : const <PujaPackage>[],
      isPurchased: json['isPurchased'],
    );
  }

  /// Cover image path or empty string.
  String get coverImage => (images == null || images!.isEmpty)
      ? ''
      : images!.first;

  /// Starting price across all attached packages.
  double? get startingPrice {
    if (packages == null || packages!.isEmpty) return null;
    double? min;
    for (final p in packages!) {
      final v = p.priceValue;
      if (v > 0 && (min == null || v < min)) {
        min = v;
      }
    }
    return min;
  }
}

/// One purchasable package attached to a puja.
class PujaPackage {
  PujaPackage({
    this.id,
    this.name,
    this.price,
    this.inclusions,
  });

  dynamic id, name, price;
  List<String>? inclusions;

  factory PujaPackage.fromJson(Map<String, dynamic> json) {
    dynamic rawInclusions =
        json['description'] ?? json['pujaPackageInclusion'] ?? json['inclusions'];

    if (rawInclusions is String && rawInclusions.trim().startsWith('[')) {
      try {
        rawInclusions = jsonDecode(rawInclusions);
      } catch (_) {}
    }

    return PujaPackage(
      id: json['id'],
      name: (json['title'] ?? json['packageName'] ?? json['name'] ?? 'Puja Package').toString(),
      price: json['package_price'] ?? json['packagePrice'] ?? json['price'] ?? '0',
      inclusions: rawInclusions is List
          ? List<String>.from(rawInclusions.map((x) => x is Map
              ? (x['name'] ?? x['inclusion'] ?? '').toString()
              : x.toString()))
          : (rawInclusions is String && rawInclusions.trim().isNotEmpty
              ? [rawInclusions.trim()]
              : const <String>[]),
    );
  }

  double get priceValue => double.tryParse(price?.toString() ?? '') ?? 0.0;
}
