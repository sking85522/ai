# NumPHP API

## Phase 1 Implemented
- Core: `array`, `ndim`, `size`, `shape`
- Creation: `empty`, `mat`, `meshgrid`, `mgrid`, `ogrid`, `bmat`
- Basic Math: `absolute`, `fix`, `ldexp`, `signbit`, `spacing`, `fmin`, `fmax`, `real`, `imag`, `conj`, `conjugate`, `remainder`, `floor_divide`, `trunc`, `angle`, `frexp`, `mod`, `around`, `divmod`, `rad2deg`, `deg2rad`, `modf`, `i0`, `nextafter`, `rint`, `float_power`, `exp2`, `log1p`, `logaddexp`, `logaddexp2`
- Comparison: `array_equal`, `array_equiv`
- Logical: `logical_xor`
- Sorting: `msort`, `sort_complex`
- Random: `random`, `randint`
- Array Manipulation: `rot90`, `row_stack`, `moveaxis`, `broadcast_arrays`, `array_split`, `fliplr`, `flipud`, `copyto`, `unique`, `broadcast_to`, `resize`, `rollaxis`
- Bitwise: `binary_repr`, `packbits`, `unpackbits`
- Types: `dtype`, `typename`, `result_type`, `promote_types`, `common_type`, `find_common_type`, `isdtype`, `issubdtype`, `issubsctype`, `issubclass_`, `min_scalar_type`, `mintypecode`, `sctype2char`, `obj2sctype`, `rank`

## Examples
```php
use NumPHP\NumPHP as np;

$a = np::array([[1,2],[3,4]]);
$abs = np::absolute(np::array([-1, 2, -3]));
$rot = np::rot90($a);
$rand = np::randint(0, 10, [2,2]);
```

## Phase 2 Implemented
- Statistics: `amin`, `amax`, `nanprod`, `nanargmax`, `nanargmin`, `nanquantile`, `nanpercentile`, `nancumprod`, `histogram2d`, `histogramdd`, `correlate`
- Indexing: `extract`, `flatnonzero`, `ravel_multi_index`, `take_along_axis`, `place`, `tril_indices`, `tril_indices_from`, `triu_indices`, `triu_indices_from`, `diag_indices`, `diag_indices_from`, `diagonal`, `indices`, `put`, `put_along_axis`, `ix_`, `unravel_index`, `mask_indices`
- Linear Algebra: `diagflat`, `matrix_transpose`, `linalg`, `einsum`, `einsum_path`, `tensordot`, `tensorsolve`, `slogdet`, `eigvals`, `eigvalsh`, `eigh`, `matrix_rank`, `vdot`, `cond`, `tensorinv`

## Phase 3 Implemented
- Remaining NumPy list items (metadata, IO helpers, datetime helpers, polynomial helpers, additional aliases).
- See `FunctionsStatus.md` for the full list.
