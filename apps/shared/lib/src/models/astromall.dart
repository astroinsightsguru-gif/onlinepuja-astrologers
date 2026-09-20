/// AstroMall (product shop) models mirroring the legacy
/// `productModel.dart` / `astromall_product_model.dart` contracts.
library;

/// Product category from `/getproductCategory`.
class ProductCategory {
  ProductCategory({this.id, this.name, this.image, this.isActive});

  dynamic id, name, image;
  dynamic isActive;

  factory ProductCategory.fromJson(Map<String, dynamic> json) =>
      ProductCategory(
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

/// Product from `/getAstromallProduct` / `/getAstromallProductById`.
class Product {
  Product({
    this.id,
    this.categoryId,
    this.name,
    this.slug,
    this.description,
    this.images,
    this.price,
    this.discountPrice,
    this.stock,
    this.isActive,
    this.createdAt,
  });

  dynamic id, categoryId, name, slug, description;
  List<String>? images;
  dynamic price, discountPrice, stock, isActive;
  dynamic createdAt;

  factory Product.fromJson(Map<String, dynamic> json) => Product(
        id: json['id'],
        categoryId: json['categoryId'] ?? json['category_id'],
        name: json['name'] ?? json['productName'],
        slug: json['slug'],
        description: json['description'] ?? json['longDescription'],
        images: json['images'] is List
            ? List<String>.from(
                json['images'].map((x) => x.toString()))
            : json['image'] == null
                ? <String>[]
                : <String>[json['image'].toString()],
        price: json['price'],
        discountPrice: json['discountPrice'] ?? json['discount_price'],
        stock: json['stock'],
        isActive: json['isActive'],
        createdAt: json['created_at'] ?? json['createdAt'],
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'categoryId': categoryId,
        'name': name,
        'slug': slug,
        'description': description,
        'images': images,
        'price': price,
        'discountPrice': discountPrice,
        'stock': stock,
        'isActive': isActive,
      };

  /// Effective display price (discounted when available).
  double get displayPrice {
    final v = (discountPrice == null ||
            discountPrice == '' ||
            double.tryParse(discountPrice.toString()) == null)
        ? price
        : discountPrice;
    return double.tryParse(v?.toString() ?? '') ?? 0.0;
  }
}
