# NumPHP

**NumPHP** is a PHP library for numerical computing, designed to provide an experience similar to Python's NumPy. It aims to be the foundational library for a future AI and data science ecosystem in PHP.

## Project Goals

- **NumPy-like API**: Familiar function names and behavior for easy adoption.
- **Multi-dimensional Arrays**: A powerful `NDArray` object as the core data structure.
- **Vectorized Operations**: Fast, element-wise mathematical and logical operations.
- **Linear Algebra**: Core matrix operations like multiplication, inverse, and determinant.
- **Statistical Functions**: Standard functions like mean, median, std, etc.

## Implemented Functions (Category Wise)

### Creation
- `array`, `zeros`, `ones`, `full`, `identity`
- `arange`, `linspace`, `logspace`

### Mathematics
- **Basic**: `add`, `subtract`, `multiply`, `divide`, `sqrt`, `power`, `abs`, `reciprocal`, `sign`, `negative`, `copysign`, `unwrap`
- **Rounding & Clipping**: `round`, `floor`, `ceil`, `clip`
- **Trigonometry**: `sin`, `cos`, `tan`, `arcsin`, `arccos`, `arctan`
- **Hyperbolic**: `sinh`, `cosh`, `tanh`
- **Exponential**: `exp`, `log`
- **Floating Point**: `isnan`, `isinf`, `isfinite`, `nan_to_num`
- **Calculus**: `diff`, `gradient`

### Linear Algebra
- `matmul` (Matrix product), `dot` (Dot product)
- `det` (Determinant), `inv` (Inverse), `solve` (Linear System Solver), `lstsq` (Least Squares)
- `trace` (Sum of diagonal), `diag` (Diagonal elements), `norm` (Matrix/Vector norm)
- `kron` (Kronecker product), `cholesky` (Cholesky Decomposition), `pinv` (Pseudo-inverse)

### Polynomials
- `polyfit` (Fit polynomial), `polyval` (Evaluate polynomial), `roots` (Find roots)

### Statistics
- `sum`, `prod`, `cumsum`, `cumprod`
- `mean`, `median`, `average`
- `var`, `std` (Variance & Standard Deviation)
- `min`, `max`, `argmin`, `argmax`, `ptp` (Peak-to-Peak)
- `percentile`, `quantile`, `cov` (Covariance), `corrcoef` (Correlation), `histogram`, `bincount`, `digitize`
- **NaN-handling**: `nansum`, `nanmean`, `nanmin`, `nanmax`, `nanstd`, `nanvar`, `nanmedian`,
- `reshape`, `flatten`, `transpose`
- `concatenate`, `vstack`, `hstack`, `column_stack`, `stack`, `split`, `hsplit`, `vsplit`
- `flip`, `roll`, `repeat`, `tile`, `ravel`, `trim_zeros`, `swapaxes`
- `expand_dims`, `squeeze`, `pad`, `append`, `delete`, `insert`, `atleast_1d`, `atleast_2d`, `atleast_3d`
## I
- `where`, `argwhere`, `nonzero`, `searchsorted`, `take`, `choose`, `compress`, `select`

### Logic & Comparison
- `equal`, `not_equal`, `greater`, `greater_equal`, `less`, `less_equal`
- `logical_and`, `logical_or`, `logical_not`re`

### Other Modules
- **Random**: `rand`, `randn`, `choice`
- **IO**: `save` (JSON), `load` (JSON), `savetxt`, `loadtxt`
- **Sorting**: `sort`, `argsort`, `partition`, `argpartition`
- **Sets**: `unique`, `intersect1d`, `setdiff1d`, `union1d`, `isin`
- **Signal Processing**:
    - **FFT**: `fftshift`
    - **Window Functions**: `hamming`, `hanning`, `blackman`, `bartlett`
- **String**: `char`, `capitalize`, `center`, `lower`, `upper`, `string_split`, `join`, `decode`, `encode`, `replace`, `strip`, `ljust`, `rjust`, `title`
- **Types**: `can_cast`, `iscomplex`, `isreal`, `finfo`, `iinfo`, `isscalar`

## Installation

```php
require_once 'modules/numphp/autoload.php';

use NumPHP\NumPHP as np;
```

## Basic Usage

Here's a quick example of how to use NumPHP:

```php
// Create a 2x3 array
$a = np::array([[1, 2, 3], [4, 5, 6]]);

// Create another array
$b = np::ones([2, 3]);

// Add them together (element-wise)
$c = np::add($a, $b);

print_r($c->getData());
```