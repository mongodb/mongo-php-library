# UPGRADE FROM 1.x to 1.15

## Method signature changes

### Parameter types

Starting with 1.14, methods now declare types for their arguments. This will not
cause BC breaks unless you've passed a type that was incompatible with the type
documented in the PHPDoc comment. 

### Return types

Return types will be added in version 2.0. These types are documented in a
PHPDoc comment, which will become the new return type. You can prepare for this
change (which will trigger a BC break in any class you may extend) by adding the
correct return type to your class at this time.

## Internal classes

Internal classes will become final where possible in a future release. At the
same time, we will add return types to these internal classes. Note that
internal classes are not covered by our backward compatibility promise, and you
should not instantiate such classes directly.
