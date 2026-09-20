/// Puja (worship booking) models mirroring the legacy
/// `pujaModel.dart` / `PujaCategoriesListModel.dart` /
/// `RecommendedPujaListModel.dart` contracts.
library;

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

  factory Puja.fromJson(Map<String, dynamic> json) => Puja(
        id: json['id'],
        categoryId: json['categoryId'] ?? json['category_id'],
        title: json['pujaTitle'] ?? json['title'] ?? json['name'],
        slug: json['slug'],
        subtitle: json['pujaSubtitle'] ?? json['subtitle'],
        place: json['pujaPlace'] ?? json['place'],
        longDescription:
            json['longDescription'] ?? json['description'],
        benefits: json['pujaBenefits'] is List
            ? List<String>.from(
                json['pujaBenefits'].map((x) => x is Map
                    ? (x['benefit'] ?? x['title'] ?? '').toString()
                    : x.toString()))
            : const <String>[],
        images: json['pujaImages'] is List
            ? List<String>.from(json['pujaImages'].map((x) => x.toString()))
            : const <String>[],
        startDatetime:
            json['pujaStartDatetime'] ?? json['startDatetime'],
        endDatetime: json['pujaEndDatetime'] ?? json['endDatetime'],
        packageIds: json['packageId'] is List
            ? List<dynamic>.from(json['packageId'])
            : const <dynamic>[],
        status: json['pujaStatus'] ?? json['status'],
        packages: json['packages'] is List
            ? List<PujaPackage>.from(
                json['packages'].map((x) => PujaPackage.fromJson(x)))
            : const <PujaPackage>[],
        isPurchased: json['isPurchased'],
      );

  /// Cover image path or empty string.
  String get coverImage => (images == null || images!.isEmpty)
      ? ''
      : images!.first;
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

  factory PujaPackage.fromJson(Map<String, dynamic> json) => PujaPackage(
        id: json['id'],
        name: json['packageName'] ?? json['name'] ?? json['title'],
        price: json['price'] ?? json['packagePrice'],
        inclusions: json['pujaPackageInclusion'] is List
            ? List<String>.from(json['pujaPackageInclusion']
                .map((x) => x is Map
                    ? (x['name'] ?? x['inclusion'] ?? '').toString()
                    : x.toString()))
            : const <String>[],
      );

  double get priceValue => double.tryParse(price?.toString() ?? '') ?? 0.0;
}
