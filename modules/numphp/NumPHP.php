<?php

namespace NumPHP;

use NumPHP\Core\NDArray;
use NumPHP\Creation\ArrayCreate;
use NumPHP\Creation\Arange;
use NumPHP\Creation\Eye;
use NumPHP\Creation\Identity;
use NumPHP\Creation\Linspace;
use NumPHP\Creation\Logspace;
use NumPHP\Math\Basic\Add;
use NumPHP\Math\Basic\Subtract;
use NumPHP\Math\Basic\Multiply;
use NumPHP\Math\Basic\Divide;
use NumPHP\Math\Trigonometry\Sin;
use NumPHP\Math\Trigonometry\Cos;
use NumPHP\Math\Trigonometry\Tan;
use NumPHP\Math\Exponential\Exp;
use NumPHP\Math\Exponential\Log;
use NumPHP\Statistics\Mean;
use NumPHP\Statistics\Median;
use NumPHP\Statistics\Std;
use NumPHP\Statistics\Var_ as VarAlias;
use NumPHP\Statistics\Max;
use NumPHP\Statistics\Min;
use NumPHP\LinearAlgebra\Matmul;
use NumPHP\ArrayManipulation\Reshape;
use NumPHP\ArrayManipulation\Transpose;
use NumPHP\ArrayManipulation\Flatten;
use NumPHP\LinearAlgebra\Dot;
use NumPHP\Random\Rand;
use NumPHP\Random\Randn;
use NumPHP\Random\Choice;
use NumPHP\ArrayManipulation\Concatenate;
use NumPHP\ArrayManipulation\Split;
use NumPHP\ArrayManipulation\Vstack;
use NumPHP\ArrayManipulation\Hstack;
use NumPHP\LinearAlgebra\Determinant;
use NumPHP\LinearAlgebra\Inverse;
use NumPHP\Statistics\Sum;
use NumPHP\Statistics\Prod;
use NumPHP\Statistics\Argmax;
use NumPHP\Statistics\Argmin;
use NumPHP\Indexing\Where;
use NumPHP\Math\Basic\Sqrt;
use NumPHP\Math\Basic\Power;
use NumPHP\Math\Basic\Abs;
use NumPHP\Math\Trigonometry\Arcsin;
use NumPHP\Math\Trigonometry\Arccos;
use NumPHP\Math\Basic\Cube;
use NumPHP\Math\Trigonometry\Arctan;
use NumPHP\IO\Save;
use NumPHP\IO\Load;
use NumPHP\Sorting\Sort;
use NumPHP\Math\Basic\Clip;
use NumPHP\Math\Basic\Round;
use NumPHP\Math\Basic\Floor;
use NumPHP\Math\Basic\Ceil;
use NumPHP\Math\Comparison\Equal;
use NumPHP\Math\Comparison\NotEqual;
use NumPHP\Math\Comparison\Greater;
use NumPHP\Math\Comparison\GreaterEqual;
use NumPHP\Math\Comparison\Less;
use NumPHP\Math\Comparison\LessEqual;
use NumPHP\Math\Logical\LogicalAnd;
use NumPHP\Math\Logical\LogicalOr;
use NumPHP\Math\Logical\LogicalNot;
use NumPHP\Math\Basic\Sign;
use NumPHP\Math\Basic\Negative;
use NumPHP\Math\Basic\Reciprocal;
use NumPHP\Statistics\Average;
use NumPHP\Sets\Unique;
use NumPHP\ArrayManipulation\Flip;
use NumPHP\ArrayManipulation\Repeat;
use NumPHP\ArrayManipulation\Tile;
use NumPHP\Math\Logical\All;
use NumPHP\Math\Logical\Any;
use NumPHP\Math\Hyperbolic\Sinh;
use NumPHP\Math\Hyperbolic\Cosh;
use NumPHP\Math\Hyperbolic\Tanh;
use NumPHP\LinearAlgebra\Trace;
use NumPHP\LinearAlgebra\Diag;
use NumPHP\Statistics\Cumsum;
use NumPHP\Statistics\Cumprod;
use NumPHP\ArrayManipulation\ExpandDims;
use NumPHP\ArrayManipulation\Squeeze;
use NumPHP\Math\FloatingPoint\Isnan;
use NumPHP\Math\FloatingPoint\Isinf;
use NumPHP\Math\FloatingPoint\Isfinite;
use NumPHP\Math\FloatingPoint\Isneginf;
use NumPHP\Math\FloatingPoint\Isposinf;
use NumPHP\Math\Calculus\Diff;
use NumPHP\LinearAlgebra\Norm;
use NumPHP\LinearAlgebra\Solve;
use NumPHP\Math\FloatingPoint\NanToNum;
use NumPHP\ArrayManipulation\Pad;
use NumPHP\ArrayManipulation\Roll;
use NumPHP\Sets\Intersect1D;
use NumPHP\Sets\Setdiff1D;
use NumPHP\Sets\Setxor1D;
use NumPHP\Sets\Union1D;
use NumPHP\LinearAlgebra\Kron;
use NumPHP\Math\Calculus\Gradient;
use NumPHP\LinearAlgebra\Cholesky;
use NumPHP\LinearAlgebra\Lstsq;
use NumPHP\SignalProcessing\FFT;
use NumPHP\Polynomial\Polyval;
use NumPHP\Polynomial\Polyfit;
use NumPHP\Polynomial\Roots;
use NumPHP\LinearAlgebra\Pinv;
use NumPHP\SignalProcessing\IFFT;
use NumPHP\SignalProcessing\FFTShift;
use NumPHP\Statistics\Quantile;
use NumPHP\Statistics\Histogram;
use NumPHP\ArrayManipulation\Append;
use NumPHP\ArrayManipulation\Delete;
use NumPHP\ArrayManipulation\Insert;
use NumPHP\Statistics\Percentile;
use NumPHP\Statistics\Covariance;
use NumPHP\Statistics\Corrcoef;
use NumPHP\SignalProcessing\Window;
use NumPHP\Math\Exponential\Log10;
use NumPHP\Math\Exponential\Log2;
use NumPHP\Math\Exponential\Expm1;
use NumPHP\Math\Exponential\Log1p;
use NumPHP\Math\Trigonometry\Hypot;
use NumPHP\Math\Trigonometry\Arctan2;
use NumPHP\Math\Basic\Degrees;
use NumPHP\Math\Basic\Radians;
use NumPHP\Math\Basic\Fmod;
use NumPHP\Bitwise\BitwiseAnd;
use NumPHP\Bitwise\BitwiseOr;
use NumPHP\Bitwise\BitwiseXor;
use NumPHP\Bitwise\Invert;
use NumPHP\Bitwise\LeftShift;
use NumPHP\Bitwise\RightShift;
use NumPHP\ArrayManipulation\Ravel;
use NumPHP\ArrayManipulation\TrimZeros;
use NumPHP\ArrayManipulation\Swapaxes;
use NumPHP\ArrayManipulation\Atleast1d;
use NumPHP\ArrayManipulation\Atleast2d;
use NumPHP\ArrayManipulation\Atleast3d;
use NumPHP\Statistics\Ptp;
use NumPHP\Statistics\Nansum;
use NumPHP\Statistics\Nanmean;
use NumPHP\Indexing\Argwhere;
use NumPHP\Indexing\Nonzero;
use NumPHP\Indexing\Searchsorted;
use NumPHP\Indexing\Take;
use NumPHP\Indexing\Choose;
use NumPHP\Indexing\Compress;
use NumPHP\Sorting\Argsort;
use NumPHP\Sorting\Partition;
use NumPHP\Sorting\Argpartition;
use NumPHP\Statistics\Nanmin;
use NumPHP\Statistics\Nanmax;
use NumPHP\Statistics\Nanstd;
use NumPHP\Statistics\Nanvar;
use NumPHP\Statistics\Nanmedian;
use NumPHP\Statistics\Bincount;
use NumPHP\IO\Savetxt;
use NumPHP\IO\Loadtxt;
use NumPHP\String\Char;
use NumPHP\String\Capitalize;
use NumPHP\String\Center;
use NumPHP\String\Lower;
use NumPHP\String\Upper;
use NumPHP\String\Split as StringSplit;
use NumPHP\String\Join;
use NumPHP\Types\CanCast;
use NumPHP\Types\IsComplex;
use NumPHP\Types\IsReal;
use NumPHP\Types\Finfo;
use NumPHP\Types\Iinfo;
use NumPHP\Types\IsScalar;
use NumPHP\String\Decode;
use NumPHP\String\Encode;
use NumPHP\String\Replace;
use NumPHP\String\Strip;
use NumPHP\Math\Basic\Copysign;
use NumPHP\Math\Basic\Unwrap;
use NumPHP\Sets\Isin;
use NumPHP\Creation\EmptyLike;
use NumPHP\Creation\FullLike;
use NumPHP\Creation\OnesLike;
use NumPHP\Creation\ZerosLike;
use NumPHP\ArrayManipulation\Stack;
use NumPHP\ArrayManipulation\ColumnStack;
use NumPHP\ArrayManipulation\Hsplit;
use NumPHP\ArrayManipulation\Vsplit;
use NumPHP\Statistics\Digitize;
use NumPHP\Statistics\Nancumsum;
use NumPHP\String\Ljust;
use NumPHP\String\Rjust;
use NumPHP\String\Title;
use NumPHP\Indexing\Select;
use NumPHP\Math\Hyperbolic\Arcsinh;
use NumPHP\Math\Hyperbolic\Arccosh;
use NumPHP\Math\Hyperbolic\Arctanh;
use NumPHP\Math\Basic\Sinc;
use NumPHP\Math\Basic\Heaviside;
use NumPHP\Math\Basic\Maximum;
use NumPHP\Math\Basic\Minimum;
use NumPHP\Creation\Tri;
use NumPHP\Creation\Tril;
use NumPHP\Creation\Triu;
use NumPHP\Creation\Vander;
use NumPHP\Creation\Geomspace;
use NumPHP\Math\Comparison\IsClose;
use NumPHP\Math\Comparison\AllClose;
use NumPHP\LinearAlgebra\MatrixPower;
use NumPHP\LinearAlgebra\Inner;
use NumPHP\LinearAlgebra\Outer;
use NumPHP\SignalProcessing\Convolve;
use NumPHP\SignalProcessing\Correlate;
use NumPHP\Math\Calculus\Trapezoid;
use NumPHP\ArrayManipulation\Block;
use NumPHP\ArrayManipulation\Dsplit;
use NumPHP\ArrayManipulation\Dstack;
use NumPHP\Indexing\FillDiagonal;
use NumPHP\Sorting\Lexsort;
use NumPHP\LinearAlgebra\Eig;
use NumPHP\LinearAlgebra\Svd;
use NumPHP\LinearAlgebra\Qr;
use NumPHP\Random\Seed;
use NumPHP\Random\Shuffle;
use NumPHP\Random\Permutation;
use NumPHP\IO\Fromfunction;


use NumPHP\IO\Fromiter;
use NumPHP\Math\Basic\Absolute;
use NumPHP\Math\Basic\Fix;
use NumPHP\Math\Basic\Ldexp;
use NumPHP\Math\Basic\Signbit;
use NumPHP\Math\Basic\Spacing;
use NumPHP\Math\Basic\Fmin;
use NumPHP\Math\Basic\Fmax;
use NumPHP\Math\Basic\Real;
use NumPHP\Math\Basic\Imag;
use NumPHP\Math\Basic\Conj;
use NumPHP\Math\Basic\Conjugate;
use NumPHP\Math\Basic\Remainder;
use NumPHP\Math\Basic\FloorDivide;
use NumPHP\Math\Basic\Trunc;
use NumPHP\Math\Basic\Angle;
use NumPHP\Math\Basic\Frexp;
use NumPHP\Math\Basic\Mod;
use NumPHP\Math\Basic\Around;
use NumPHP\Math\Basic\Divmod;
use NumPHP\Math\Basic\Rad2Deg;
use NumPHP\Math\Basic\Deg2Rad;
use NumPHP\Math\Basic\Modf;
use NumPHP\Math\Basic\I0;
use NumPHP\Math\Basic\Nextafter;
use NumPHP\Math\Basic\Rint;
use NumPHP\Math\Basic\FloatPower;
use NumPHP\Math\Exponential\Exp2;
use NumPHP\Math\Exponential\Logaddexp;
use NumPHP\Math\Exponential\Logaddexp2;
use NumPHP\Math\Comparison\ArrayEqual;
use NumPHP\Math\Comparison\ArrayEquiv;
use NumPHP\Math\Logical\LogicalXor;
use NumPHP\Sorting\Msort;
use NumPHP\Sorting\SortComplex;
use NumPHP\Random\Random;
use NumPHP\Random\Randint;
use NumPHP\Creation\Empty_;
use NumPHP\Creation\Array;
use NumPHP\Creation\Mat;
use NumPHP\Creation\Meshgrid;
use NumPHP\Creation\Mgrid;
use NumPHP\Creation\Ogrid;
use NumPHP\Creation\Bmat;
use NumPHP\ArrayManipulation\Rot90;
use NumPHP\ArrayManipulation\RowStack;
use NumPHP\ArrayManipulation\Moveaxis;
use NumPHP\ArrayManipulation\BroadcastArrays;
use NumPHP\ArrayManipulation\ArraySplit;
use NumPHP\ArrayManipulation\Ndim;
use NumPHP\ArrayManipulation\Size;
use NumPHP\ArrayManipulation\Fliplr;
use NumPHP\ArrayManipulation\Flipud;
use NumPHP\ArrayManipulation\Copyto;
use NumPHP\ArrayManipulation\Unique as UniqueArray;
use NumPHP\ArrayManipulation\Shape;
use NumPHP\ArrayManipulation\BroadcastTo;
use NumPHP\ArrayManipulation\Resize;
use NumPHP\ArrayManipulation\Rollaxis;
use NumPHP\Bitwise\BinaryRepr;
use NumPHP\Bitwise\Packbits;
use NumPHP\Bitwise\Unpackbits;
use NumPHP\Types\Dtype;
use NumPHP\Types\Typename;
use NumPHP\Types\ResultType;
use NumPHP\Types\PromoteTypes;
use NumPHP\Types\CommonType;
use NumPHP\Types\FindCommonType;
use NumPHP\Types\Isdtype;
use NumPHP\Types\Issubdtype;
use NumPHP\Types\Issubsctype;
use NumPHP\Types\Issubclass;
use NumPHP\Types\MinScalarType;
use NumPHP\Types\Mintypecode;
use NumPHP\Types\Sctype2Char;
use NumPHP\Types\Obj2Sctype;
use NumPHP\Types\Rank;
use NumPHP\Statistics\Amin;
use NumPHP\Statistics\Amax;
use NumPHP\Statistics\Nanprod;
use NumPHP\Statistics\Nanargmax;
use NumPHP\Statistics\Nanargmin;
use NumPHP\Statistics\Nanquantile;
use NumPHP\Statistics\Nanpercentile;
use NumPHP\Statistics\Nancumprod;
use NumPHP\Statistics\Histogram2D;
use NumPHP\Statistics\Histogramdd;
use NumPHP\Statistics\Correlate as StatsCorrelate;
use NumPHP\Indexing\Extract;
use NumPHP\Indexing\Flatnonzero;
use NumPHP\Indexing\RavelMultiIndex;
use NumPHP\Indexing\TakeAlongAxis;
use NumPHP\Indexing\Place;
use NumPHP\Indexing\TrilIndices;
use NumPHP\Indexing\TrilIndicesFrom;
use NumPHP\Indexing\TriuIndices;
use NumPHP\Indexing\TriuIndicesFrom;
use NumPHP\Indexing\DiagIndices;
use NumPHP\Indexing\DiagIndicesFrom;
use NumPHP\Indexing\Diagonal;
use NumPHP\Indexing\Indices;
use NumPHP\Indexing\Put;
use NumPHP\Indexing\PutAlongAxis;
use NumPHP\Indexing\Ix;
use NumPHP\Indexing\UnravelIndex;
use NumPHP\Indexing\MaskIndices;
use NumPHP\LinearAlgebra\Diagflat;
use NumPHP\LinearAlgebra\MatrixTranspose;
use NumPHP\LinearAlgebra\Linalg;
use NumPHP\LinearAlgebra\Einsum;
use NumPHP\LinearAlgebra\EinsumPath;
use NumPHP\LinearAlgebra\Tensordot;
use NumPHP\LinearAlgebra\Tensorsolve;
use NumPHP\LinearAlgebra\Slogdet;
use NumPHP\LinearAlgebra\Eigvals;
use NumPHP\LinearAlgebra\Eigvalsh;
use NumPHP\LinearAlgebra\Eigh;
use NumPHP\LinearAlgebra\MatrixRank;
use NumPHP\LinearAlgebra\Vdot;
use NumPHP\LinearAlgebra\Cond;
use NumPHP\LinearAlgebra\Tensorinv;
use NumPHP\Utils\Helpers;




class NumPHP
{
    /**
     * Creates an array.
     *
     * @param mixed $data
     * @param mixed $dtype
     * @return NDArray
     */
    public static function array($data, $dtype = null): NDArray
    {
        return new NDArray($data, $dtype);
    }

    /**
     * Return a new array of given shape and type, filled with zeros.
     *
     * @param array $shape
     * @param string|null $dtype
     * @return NDArray
     */
    public static function zeros(array $shape, string $dtype = null): NDArray
    {
        return ArrayCreate::zeros($shape, $dtype);
    }

    /**
     * Return a new array of given shape and type, filled with ones.
     *
     * @param array $shape
     * @param string|null $dtype
     * @return NDArray
     */
    public static function ones(array $shape, string $dtype = null): NDArray
    {
        return ArrayCreate::ones($shape, $dtype);
    }

    /**
     * Return a new array of given shape and type, filled with a custom value.
     *
     * @param array $shape
     * @param mixed $value
     * @param string|null $dtype
     * @return NDArray
     */
    public static function full(array $shape, $value, string $dtype = null): NDArray
    {
        return ArrayCreate::full($shape, $value, $dtype);
    }

    /**
     * Return evenly spaced values within a given interval.
     *
     * @param int $start
     * @param int $stop
     * @param int $step
     * @param string|null $dtype
     * @return NDArray
     */
    public static function arange($start, $stop = null, $step = 1, $dtype = null): NDArray
    {
        return Arange::arange($start, $stop, $step, $dtype);
    }

    /**
     * Return a 2-D array with ones on the diagonal and zeros elsewhere.
     *
     * @param int $N
     * @param int|null $M
     * @param int $k
     * @param string|null $dtype
     * @return NDArray
     */
    public static function eye($N, $M = null, $k = 0, $dtype = null): NDArray
    {
        return Eye::eye($N, $M, $k, $dtype);
    }

    /**
     * Return the identity array.
     *
     * @param int $n
     * @param string|null $dtype
     * @return NDArray
     */
    public static function identity($n, $dtype = null): NDArray
    {
        return Identity::identity($n, $dtype);
    }

    /**
     * Return evenly spaced numbers over a specified interval.
     *
     * @param int $start
     * @param int $stop
     * @param int $num
     * @param bool $endpoint
     * @param bool $retstep
     * @param string|null $dtype
     * @return NDArray
     */
    public static function linspace($start, $stop, $num = 50, $endpoint = true, $retstep = false, $dtype = null): NDArray
    {
        return Linspace::linspace($start, $stop, $num, $endpoint, $retstep, $dtype);
    }

    /**
     * Return numbers spaced evenly on a log scale.
     *
     * @param int $start
     * @param int $stop
     * @param int $num
     * @param float $base
     * @param bool $endpoint
     * @param string|null $dtype
     * @return NDArray
     */
    public static function logspace($start, $stop, $num = 50, $base = 10.0, $endpoint = true, $dtype = null): NDArray
    {
        return Logspace::logspace($start, $stop, $num, $base, $endpoint, $dtype);
    }

    /**
     * Adds two arrays element-wise.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return NDArray
     */
    public static function add(NDArray $a, NDArray $b): NDArray
    {
        return Add::add($a, $b);
    }

    /**
     * Subtracts two arrays element-wise.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return NDArray
     */
    public static function subtract(NDArray $a, NDArray $b): NDArray
    {
        return Subtract::subtract($a, $b);
    }

    /**
     * Multiplies two arrays element-wise.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return NDArray
     */
    public static function multiply(NDArray $a, NDArray $b): NDArray
    {
        return Multiply::multiply($a, $b);
    }

    /**
     * Divides two arrays element-wise.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return NDArray
     */
    public static function divide(NDArray $a, NDArray $b): NDArray
    {
        return Divide::divide($a, $b);
    }

    /**
     * Computes the sine of an array element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function sin(NDArray $a): NDArray
    {
        return Sin::sin($a);
    }

    /**
     * Computes the cosine of an array element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function cos(NDArray $a): NDArray
    {
        return Cos::cos($a);
    }

    /**
     * Computes the tangent of an array element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function tan(NDArray $a): NDArray
    {
        return Tan::tan($a);
    }

    /**
     * Computes the exponential of an array element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function exp(NDArray $a): NDArray
    {
        return Exp::exp($a);
    }

    /**
     * Computes the natural logarithm of an array element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function log(NDArray $a): NDArray
    {
        return Log::log($a);
    }

    /**
     * Computes the arithmetic mean of an array.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return float|NDArray
     */
    public static function mean(NDArray $a, ?int $axis = null)
    {
        return Mean::mean($a, $axis);
    }

    /**
     * Computes the median of an array.
     *
     * @param NDArray $a
     * @return float
     */
    public static function median(NDArray $a): float
    {
        return Median::median($a);
    }

    /**
     * Computes the standard deviation of an array.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return float|NDArray
     */
    public static function std(NDArray $a, ?int $axis = null)
    {
        return Std::std($a, $axis);
    }

    /**
     * Computes the variance of an array.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return float|NDArray
     */
    public static function var(NDArray $a, ?int $axis = null)
    {
        return VarAlias::var($a, $axis);
    }

    /**
     * Finds the maximum value of an array.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return mixed
     */
    public static function max(NDArray $a, ?int $axis = null)
    {
        return Max::max($a, $axis);
    }

    /**
     * Finds the minimum value of an array.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return mixed
     */
    public static function min(NDArray $a, ?int $axis = null)
    {
        return Min::min($a, $axis);
    }

    /**
     * Matrix product of two arrays.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return NDArray
     */
    public static function matmul(NDArray $a, NDArray $b): NDArray
    {
        return Matmul::matmul($a, $b);
    }

    /**
     * Gives a new shape to an array without changing its data.
     *
     * @param NDArray $a
     * @param array $newShape
     * @return NDArray
     */
    public static function reshape(NDArray $a, array $newShape): NDArray
    {
        return Reshape::reshape($a, $newShape);
    }

    /**
     * Reverse or permute the axes of an array; returns the modified array.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function transpose(NDArray $a): NDArray
    {
        return Transpose::transpose($a);
    }

    /**
     * Return a copy of the array collapsed into one dimension.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function flatten(NDArray $a): NDArray
    {
        return Flatten::flatten($a);
    }

    /**
     * Random values in a given shape.
     *
     * @param array $shape
     * @return NDArray
     */
    public static function rand(array $shape): NDArray
    {
        return Rand::rand($shape);
    }

    /**
     * Return a sample (or samples) from the "standard normal" distribution.
     *
     * @param array $shape
     * @return NDArray
     */
    public static function randn(array $shape): NDArray
    {
        return Randn::randn($shape);
    }

    /**
     * Generates a random sample from a given 1-D array.
     *
     * @param mixed $a
     * @param int|array|null $size
     * @param bool $replace
     * @param array|null $p
     * @return NDArray
     */
    public static function choice($a, $size = null, bool $replace = true, array $p = null): NDArray
    {
        return Choice::choice($a, $size, $replace, $p);

    }



    /**
     * Join a sequence of arrays along an existing axis.
     *
     * @param array $arrays Sequence of NDArrays
     * @param int $axis
     * @return NDArray
     */
    public static function concatenate(array $arrays, int $axis = 0): NDArray
    {
        return Concatenate::concatenate($arrays, $axis);
    }

    /**
     * Split an array into multiple sub-arrays.
     *
     * @param NDArray $ary
     * @param int|array $indices_or_sections
     * @param int $axis
     * @return array Array of NDArrays
     */
    public static function split(NDArray $ary, $indices_or_sections, int $axis = 0): array
    {
        return Split::split($ary, $indices_or_sections, $axis);
    }

    /**
     * Compute the determinant of an array.
     *
     * @param NDArray $a
     * @return float
     */
    public static function det(NDArray $a): float
    {
        return Determinant::det($a);
    }

    /**
     * Compute the (multiplicative) inverse of a matrix.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function inv(NDArray $a): NDArray
    {
        return Inverse::inverse($a);
    }

    /**
     * Sum of array elements.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return float|NDArray
     */
    public static function sum(NDArray $a, ?int $axis = null)
    {
        return Sum::sum($a, $axis);
    }

    /**
     * Return the product of array elements.
     *
     * @param NDArray $a
     * @return float
     */
    public static function prod(NDArray $a): float
    {
        return Prod::prod($a);
    }

    /**
     * Return elements chosen from x or y depending on condition.
     *
     * @param NDArray $condition
     * @param mixed $x NDArray or scalar
     * @param mixed $y NDArray or scalar
     * @return NDArray
     */
    public static function where(NDArray $condition, $x, $y): NDArray
    {
        return Where::where($condition, $x, $y);
    }

    /**
     * Dot product of two arrays.
     *
     * @param mixed $a NDArray or scalar
     * @param mixed $b NDArray or scalar
     * @return mixed Float/Int for vector dot, NDArray for matrix mul
     */
    public static function dot($a, $b)
    {
        return Dot::dot($a, $b);
    }

    /**
     * Returns the indices of the maximum values along an axis.
     *
     * @param NDArray $a
     * @return int
     */
    public static function argmax(NDArray $a): int
    {
        return Argmax::argmax($a);
    }

    /**
     * Returns the indices of the minimum values along an axis.
     *
     * @param NDArray $a
     * @return int
     */
    public static function argmin(NDArray $a): int
    {
        return Argmin::argmin($a);
    }

    /**
     * Return the non-negative square-root of an array, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function sqrt(NDArray $a): NDArray
    {
        return Sqrt::sqrt($a);
    }

    /**
     * First array elements raised to powers from second array (or scalar), element-wise.
     *
     * @param NDArray $a
     * @param mixed $exponent
     * @return NDArray
     */
    public static function power(NDArray $a, $exponent): NDArray
    {
        return Power::power($a, $exponent);
    }

    /**
     * Calculate the absolute value element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function abs(NDArray $a): NDArray
    {
        return Abs::abs($a);
    }

    /**
     * Stack arrays in sequence vertically (row wise).
     *
     * @param array $tup Sequence of NDArrays.
     * @return NDArray
     */
    public static function vstack(array $tup): NDArray
    {
        return Vstack::vstack($tup);
    }

    /**
     * Stack arrays in sequence horizontally (column wise).
     *
     * @param array $tup Sequence of NDArrays.
     * @return NDArray
     */
    public static function hstack(array $tup): NDArray
    {
        return Hstack::hstack($tup);
    }

    /**
     * Inverse sine, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function arcsin(NDArray $a): NDArray
    {
        return Arcsin::arcsin($a);
    }

    /**
     * Inverse cosine, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function arccos(NDArray $a): NDArray
    {
        return Arccos::arccos($a);
    }

    /**
     * Inverse tangent, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function arctan(NDArray $a): NDArray
    {
        return Arctan::arctan($a);
    }

    /**
     * Cubes of x, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function cube(NDArray $a): NDArray
    {
        return Cube::cube($a);
    }

    /**
     * Save an array to a text file in JSON format.
     *
     * @param string $file
     * @param NDArray $arr
     */
    public static function save(string $file, NDArray $arr): void
    {
        Save::save($file, $arr);
    }

    /**
     * Load an array from a text file (JSON format).
     *
     * @param string $file
     * @return NDArray
     */
    public static function load(string $file): NDArray
    {
        return Load::load($file);
    }

    /**
     * Return a sorted copy of an array.
     *
     * @param NDArray $a
     * @param int|null $axis Axis along which to sort. -1 means last axis. null means flatten.
     * @return NDArray
     */
    public static function sort(NDArray $a, ?int $axis = -1): NDArray
    {
        return Sort::sort($a, $axis);
    }

    /**
     * Clip (limit) the values in an array.
     *
     * @param NDArray $a
     * @param float|int|null $min
     * @param float|int|null $max
     * @return NDArray
     */
    public static function clip(NDArray $a, $min, $max): NDArray
    {
        return Clip::clip($a, $min, $max);
    }

    /**
     * Evenly round to the given number of decimals.
     *
     * @param NDArray $a
     * @param int $decimals
     * @return NDArray
     */
    public static function round(NDArray $a, int $decimals = 0): NDArray
    {
        return Round::round($a, $decimals);
    }

    /**
     * Return the floor of the input, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function floor(NDArray $a): NDArray
    {
        return Floor::floor($a);
    }

    /**
     * Return the ceiling of the input, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function ceil(NDArray $a): NDArray
    {
        return Ceil::ceil($a);
    }

    /**
     * Return the truth value of (a == b) element-wise.
     *
     * @param NDArray $a
     * @param mixed $b
     * @return NDArray
     */
    public static function equal(NDArray $a, $b): NDArray
    {
        return Equal::equal($a, $b);
    }

    /**
     * Return the truth value of (a != b) element-wise.
     *
     * @param NDArray $a
     * @param mixed $b
     * @return NDArray
     */
    public static function not_equal(NDArray $a, $b): NDArray
    {
        return NotEqual::not_equal($a, $b);
    }

    /**
     * Return the truth value of (a > b) element-wise.
     *
     * @param NDArray $a
     * @param mixed $b
     * @return NDArray
     */
    public static function greater(NDArray $a, $b): NDArray
    {
        return Greater::greater($a, $b);
    }

    /**
     * Return the truth value of (a >= b) element-wise.
     *
     * @param NDArray $a
     * @param mixed $b
     * @return NDArray
     */
    public static function greater_equal(NDArray $a, $b): NDArray
    {
        return GreaterEqual::greater_equal($a, $b);
    }

    /**
     * Return the truth value of (a < b) element-wise.
     *
     * @param NDArray $a
     * @param mixed $b
     * @return NDArray
     */
    public static function less(NDArray $a, $b): NDArray
    {
        return Less::less($a, $b);
    }

    /**
     * Return the truth value of (a <= b) element-wise.
     *
     * @param NDArray $a
     * @param mixed $b
     * @return NDArray
     */
    public static function less_equal(NDArray $a, $b): NDArray
    {
        return LessEqual::less_equal($a, $b);
    }

    /**
     * Compute the truth value of a AND b element-wise.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return NDArray
     */
    public static function logical_and(NDArray $a, NDArray $b): NDArray
    {
        return LogicalAnd::logical_and($a, $b);
    }

    /**
     * Compute the truth value of a OR b element-wise.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return NDArray
     */
    public static function logical_or(NDArray $a, NDArray $b): NDArray
    {
        return LogicalOr::logical_or($a, $b);
    }

    /**
     * Compute the truth value of NOT x element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function logical_not(NDArray $a): NDArray
    {
        return LogicalNot::logical_not($a);
    }

    /**
     * Returns an element-wise indication of the sign of a number.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function sign(NDArray $a): NDArray
    {
        return Sign::sign($a);
    }

    /**
     * Numerical negative, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function negative(NDArray $a): NDArray
    {
        return Negative::negative($a);
    }

    /**
     * Return the reciprocal of the argument, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function reciprocal(NDArray $a): NDArray
    {
        return Reciprocal::reciprocal($a);
    }

    /**
     * Compute the weighted average along the specified axis.
     *
     * @param NDArray $a
     * @param NDArray|array|null $weights
     * @return float
     */
    public static function average(NDArray $a, $weights = null): float
    {
        return Average::average($a, $weights);
    }

    /**
     * Find the unique elements of an array.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function unique(NDArray $a): NDArray
    {
        return Unique::unique($a);
    }

    /**
     * Reverse the order of elements in an array along the given axis.
     *
     * @param NDArray $m
     * @param int|null $axis
     * @return NDArray
     */
    public static function flip(NDArray $m, ?int $axis = null): NDArray
    {
        return Flip::flip($m, $axis);
    }

    /**
     * Repeat elements of an array.
     *
     * @param NDArray $a
     * @param int $repeats
     * @return NDArray
     */
    public static function repeat(NDArray $a, int $repeats): NDArray
    {
        return Repeat::repeat($a, $repeats);
    }

    /**
     * Construct an array by repeating A the number of times given by reps.
     *
     * @param NDArray $a
     * @param int $reps
     * @return NDArray
     */
    public static function tile(NDArray $a, int $reps): NDArray
    {
        return Tile::tile($a, $reps);
    }

    /**
     * Test whether all array elements along a given axis evaluate to True.
     *
     * @param NDArray $a
     * @return bool
     */
    public static function all(NDArray $a): bool
    {
        return All::all($a);
    }






    /**
     * Test whether any array element along a given axis evaluates to True.
     *
     * @param NDArray $a
     * @return bool
     */
    public static function any(NDArray $a): bool
    {
        return Any::any($a);
    }

    /**
     * Hyperbolic sine, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function sinh(NDArray $a): NDArray
    {
        return Sinh::sinh($a);
    }

    /**
     * Hyperbolic cosine, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function cosh(NDArray $a): NDArray
    {
        return Cosh::cosh($a);
    }

    /**
     * Hyperbolic tangent, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function tanh(NDArray $a): NDArray
    {
        return Tanh::tanh($a);
    }

    /**
     * Return the sum along diagonals of the array.
     *
     * @param NDArray $a
     * @return float
     */
    public static function trace(NDArray $a): float
    {
        return Trace::trace($a);
    }

    /**
     * Extract a diagonal or construct a diagonal array.
     *
     * @param NDArray $v
     * @param int $k
     * @return NDArray
     */
    public static function diag(NDArray $v, int $k = 0): NDArray
    {
        return Diag::diag($v, $k);
    }

    /**
     * Expand the shape of an array.
     *
     * @param NDArray $a
     * @param int $axis
     * @return NDArray
     */
    public static function expand_dims(NDArray $a, int $axis): NDArray
    {
        return ExpandDims::expand_dims($a, $axis);
    }

    /**
     * Remove single-dimensional entries from the shape of an array.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function squeeze(NDArray $a): NDArray
    {
        return Squeeze::squeeze($a);
    }

    /**
     * Return the cumulative sum of the elements along a given axis.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return NDArray
     */
    public static function cumsum(NDArray $a, ?int $axis = null): NDArray
    {
        return Cumsum::cumsum($a, $axis);
    }

    /**
     * Return the cumulative product of elements along a given axis.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return NDArray
     */
    public static function cumprod(NDArray $a, ?int $axis = null): NDArray
    {
        return Cumprod::cumprod($a, $axis);
    }

    /**
     * Test element-wise for NaN and return result as a boolean array.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function isnan(NDArray $a): NDArray
    {
        return Isnan::isnan($a);
    }

    /**
     * Test element-wise for positive or negative infinity.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function isinf(NDArray $a): NDArray
    {
        return Isinf::isinf($a);
    }

    /**
     * Test element-wise for finiteness (not infinity or not Not a Number).
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function isfinite(NDArray $a): NDArray
    {
        return Isfinite::isfinite($a);
    }

    /**
     * Calculate the n-th discrete difference along the given axis.
     *
     * @param NDArray $a
     * @param int $n
     * @return NDArray
     */
    public static function diff(NDArray $a, int $n = 1): NDArray
    {
        return Diff::diff($a, $n);
    }

    /**
     * Matrix or vector norm.
     *
     * @param NDArray $x
     * @return float
     */
    public static function norm(NDArray $x): float
    {
        return Norm::norm($x);
    }

    /**
     * Solve a linear matrix equation, or system of linear scalar equations.
     * Computes the "exact" solution, x, of the well-determined, i.e., full rank, linear matrix equation ax = b.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return NDArray
     */
    public static function solve(NDArray $a, NDArray $b): NDArray
    {
        return Solve::solve($a, $b);
    }

    /**
     * Replace NaN with zero and infinity with large finite numbers.
     *
     * @param NDArray $a
     * @param float $nan
     * @param float|null $posinf
     * @param float|null $neginf
     * @return NDArray
     */
    public static function nan_to_num(NDArray $a, float $nan = 0.0, ?float $posinf = null, ?float $neginf = null): NDArray
    {
        return NanToNum::nan_to_num($a, $nan, $posinf, $neginf);
    }

    /**
     * Pad an array.
     *
     * @param NDArray $array
     * @param int|array $pad_width
     * @param string $mode
     * @param array $constant_values
     * @return NDArray
     */
    public static function pad(NDArray $array, $pad_width, string $mode = 'constant', array $constant_values = [0]): NDArray
    {
        return Pad::pad($array, $pad_width, $mode, $constant_values);
    }

    /**
     * Roll array elements along a given axis.
     *
     * @param NDArray $a
     * @param int $shift
     * @return NDArray
     */
    public static function roll(NDArray $a, int $shift): NDArray
    {
        return Roll::roll($a, $shift);
    }

    /**
     * Find the intersection of two arrays.
     *
     * @param NDArray $ar1
     * @param NDArray $ar2
     * @return NDArray
     */
    public static function intersect1d(NDArray $ar1, NDArray $ar2): NDArray
    {
        return Intersect1D::intersect1d($ar1, $ar2);
    }

    /**
     * Find the set difference of two arrays.
     *
     * @param NDArray $ar1
     * @param NDArray $ar2
     * @return NDArray
     */
    public static function setdiff1d(NDArray $ar1, NDArray $ar2): NDArray
    {
        return Setdiff1D::setdiff1d($ar1, $ar2);
    }

    /**
     * Find the union of two arrays.
     *
     * @param NDArray $ar1
     * @param NDArray $ar2
     * @return NDArray
     */
    public static function union1d(NDArray $ar1, NDArray $ar2): NDArray
    {
        return Union1D::union1d($ar1, $ar2);
    }

    /**
     * Kronecker product of two arrays.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return NDArray
     */
    public static function kron(NDArray $a, NDArray $b): NDArray
    {
        return Kron::kron($a, $b);
    }

    /**
     * Return the gradient of an N-dimensional array.
     *
     * @param NDArray $f
     * @return NDArray
     */
    public static function gradient(NDArray $f): NDArray
    {
        return Gradient::gradient($f);
    }

    /**
     * Cholesky decomposition.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function cholesky(NDArray $a): NDArray
    {
        return Cholesky::cholesky($a);
    }

    /**
     * Return the least-squares solution to a linear matrix equation.
     *
     * @param NDArray $a
     * @param NDArray $b
     * @return NDArray
     */
    public static function lstsq(NDArray $a, NDArray $b): NDArray
    {
        return Lstsq::lstsq($a, $b);
    }

    /**
     * Compute the one-dimensional discrete Fourier Transform.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function fft(NDArray $a): NDArray
    {
        return FFT::fft($a);
    }

    /**
     * Evaluate a polynomial at specific values.
     *
     * @param NDArray|array $p
     * @param NDArray|float|int $x
     * @return NDArray
     */
    public static function polyval($p, $x): NDArray
    {
        return Polyval::polyval($p, $x);
    }

    /**
     * Append values to the end of an array.
     *
     * @param NDArray $arr
     * @param mixed $values
     * @param int|null $axis
     * @return NDArray
     */
    public static function append(NDArray $arr, $values, ?int $axis = null): NDArray
    {
        return Append::append($arr, $values, $axis);
    }

    /**
     * Return a new array with sub-arrays along an axis deleted.
     *
     * @param NDArray $arr
     * @param int|array $obj
     * @param int|null $axis
     * @return NDArray
     */
    public static function delete(NDArray $arr, $obj, ?int $axis = null): NDArray
    {
        return Delete::delete($arr, $obj, $axis);
    }

    /**
     * Insert values along the given axis before the given indices.
     *
     * @param NDArray $arr
     * @param int $obj
     * @param mixed $values
     * @param int|null $axis
     * @return NDArray
     */
    public static function insert(NDArray $arr, int $obj, $values, ?int $axis = null): NDArray
    {
        return Insert::insert($arr, $obj, $values, $axis);
    }

    /**
     * Compute the q-th percentile of the data.
     *
     * @param NDArray $a
     * @param float $q
     * @return float
     */
    public static function percentile(NDArray $a, float $q): float
    {
        return Percentile::percentile($a, $q);
    }

    /**
     * Estimate a covariance matrix.
     *
     * @param NDArray $m
     * @return NDArray
     */
    public static function cov(NDArray $m): NDArray
    {
        return Covariance::cov($m);
    }

    /**
     * Return Pearson product-moment correlation coefficients.
     *
     * @param NDArray $x
     * @return NDArray
     */
    public static function corrcoef(NDArray $x): NDArray
    {
        return Corrcoef::corrcoef($x);
    }

    /**
     * Return the Hamming window.
     *
     * @param int $M
     * @return NDArray
     */
    public static function hamming(int $M): NDArray
    {
        return Window::hamming($M);
    }

    /**
     * Return the Hanning window.
     *
     * @param int $M
     * @return NDArray
     */
    public static function hanning(int $M): NDArray
    {
        return Window::hanning($M);
    }

    /**
     * Return the Blackman window.
     *
     * @param int $M
     * @return NDArray
     */
    public static function blackman(int $M): NDArray
    {
        return Window::blackman($M);
    }

    /**
     * Return the Bartlett window.
     *
     * @param int $M
     * @return NDArray
     */
    public static function bartlett(int $M): NDArray
    {
        return Window::bartlett($M);
    }

    /**
     * Least squares polynomial fit.
     *
     * @param NDArray $x
     * @param NDArray $y
     * @param int $deg
     * @return NDArray
     */
    public static function polyfit(NDArray $x, NDArray $y, int $deg): NDArray
    {
        return Polyfit::polyfit($x, $y, $deg);
    }

    /**
     * Return the roots of a polynomial with the given coefficients.
     *
     * @param NDArray|array $p
     * @return NDArray
     */
    public static function roots($p): NDArray
    {
        return Roots::roots($p);
    }

    /**
     * Compute the (Moore-Penrose) pseudo-inverse of a matrix.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function pinv(NDArray $a): NDArray
    {
        return Pinv::pinv($a);
    }

    /**
     * Compute the one-dimensional inverse discrete Fourier Transform.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function ifft(NDArray $a): NDArray
    {
        return IFFT::ifft($a);
    }

    /**
     * Shift the zero-frequency component to the center of the spectrum.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function fftshift(NDArray $a): NDArray
    {
        return FFTShift::fftshift($a);
    }

    /**
     * Compute the q-th quantile of the data.
     *
     * @param NDArray $a
     * @param float $q
     * @return float
     */
    public static function quantile(NDArray $a, float $q): float
    {
        return Quantile::quantile($a, $q);
    }

    /**
     * Compute the histogram of a set of data.
     *
     * @param NDArray $a
     * @param int $bins
     * @return array
     */
    public static function histogram(NDArray $a, int $bins = 10): array
    {
        return Histogram::histogram($a, $bins);
    }

    /**
     * Return the base 10 logarithm of the input, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function log10(NDArray $a): NDArray
    {
        return Log10::log10($a);
    }

    /**
     * Return the base 2 logarithm of the input, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function log2(NDArray $a): NDArray
    {
        return Log2::log2($a);
    }

    /**
     * Calculate exp(x) - 1 for all elements in the array.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function expm1(NDArray $a): NDArray
    {
        return Expm1::expm1($a);
    }

    /**
     * Return the natural logarithm of one plus the input array, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function log1p(NDArray $a): NDArray
    {
        return Log1p::log1p($a);
    }

    /**
     * Given the "legs" of a right triangle, return its hypotenuse.
     *
     * @param NDArray $x1
     * @param NDArray $x2
     * @return NDArray
     */
    public static function hypot(NDArray $x1, NDArray $x2): NDArray
    {
        return Hypot::hypot($x1, $x2);
    }

    /**
     * Element-wise arc tangent of x1/x2 choosing the quadrant correctly.
     *
     * @param NDArray $x1
     * @param NDArray $x2
     * @return NDArray
     */
    public static function arctan2(NDArray $x1, NDArray $x2): NDArray
    {
        return Arctan2::arctan2($x1, $x2);
    }

    /**
     * Convert angles from radians to degrees.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function degrees(NDArray $a): NDArray
    {
        return Degrees::degrees($a);
    }

    /**
     * Convert angles from degrees to radians.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function radians(NDArray $a): NDArray
    {
        return Radians::radians($a);
    }

    /**
     * Compute the floating-point remainder of division, element-wise.
     *
     * @param NDArray $x1
     * @param NDArray $x2
     * @return NDArray
     */
    public static function fmod(NDArray $x1, NDArray $x2): NDArray
    {
        return Fmod::fmod($x1, $x2);
    }

    /**
     * Compute the bit-wise AND of two arrays element-wise.
     *
     * @param NDArray $a
     * @param mixed $b
     * @return NDArray
     */
    public static function bitwise_and(NDArray $a, $b): NDArray
    {
        return BitwiseAnd::bitwise_and($a, $b);
    }

    /**
     * Compute the bit-wise OR of two arrays element-wise.
     *
     * @param NDArray $a
     * @param mixed $b
     * @return NDArray
     */
    public static function bitwise_or(NDArray $a, $b): NDArray
    {
        return BitwiseOr::bitwise_or($a, $b);
    }

    /**
     * Compute the bit-wise XOR of two arrays element-wise.
     *
     * @param NDArray $a
     * @param mixed $b
     * @return NDArray
     */
    public static function bitwise_xor(NDArray $a, $b): NDArray
    {
        return BitwiseXor::bitwise_xor($a, $b);
    }

    /**
     * Compute bit-wise inversion, or bit-wise NOT, element-wise.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function invert(NDArray $a): NDArray
    {
        return Invert::invert($a);
    }

    /**
     * Shift the bits of an integer to the left.
     *
     * @param NDArray $a
     * @param mixed $b
     * @return NDArray
     */
    public static function left_shift(NDArray $a, $b): NDArray
    {
        return LeftShift::left_shift($a, $b);
    }

    /**
     * Shift the bits of an integer to the right.
     *
     * @param NDArray $a
     * @param mixed $b
     * @return NDArray
     */
    public static function right_shift(NDArray $a, $b): NDArray
    {
        return RightShift::right_shift($a, $b);
    }

    /**
     * Return a contiguous flattened array.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function ravel(NDArray $a): NDArray
    {
        return Ravel::ravel($a);
    }

    /**
     * Trim the leading and/or trailing zeros from a 1-D array.
     *
     * @param NDArray $a
     * @param string $trim
     * @return NDArray
     */
    public static function trim_zeros(NDArray $a, string $trim = 'fb'): NDArray
    {
        return TrimZeros::trim_zeros($a, $trim);
    }

    /**
     * Interchange two axes of an array.
     *
     * @param NDArray $a
     * @param int $axis1
     * @param int $axis2
     * @return NDArray
     */
    public static function swapaxes(NDArray $a, int $axis1, int $axis2): NDArray
    {
        return Swapaxes::swapaxes($a, $axis1, $axis2);
    }

    /**
     * Convert inputs to arrays with at least one dimension.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function atleast_1d(NDArray $a): NDArray
    {
        return Atleast1d::atleast_1d($a);
    }

    /**
     * View inputs as arrays with at least two dimensions.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function atleast_2d(NDArray $a): NDArray
    {
        return Atleast2d::atleast_2d($a);
    }

    /**
     * View inputs as arrays with at least three dimensions.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function atleast_3d(NDArray $a): NDArray
    {
        return Atleast3d::atleast_3d($a);
    }

    /**
     * Range of values (maximum - minimum) along an axis.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return mixed
     */
    public static function ptp(NDArray $a, ?int $axis = null)
    {
        return Ptp::ptp($a, $axis);
    }

    /**
     * Find the indices of array elements that are non-zero, grouped by element.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function argwhere(NDArray $a): NDArray
    {
        return Argwhere::argwhere($a);
    }

    /**
     * Return the indices of the elements that are non-zero.
     *
     * @param NDArray $a
     * @return array
     */
    public static function nonzero(NDArray $a): array
    {
        return Nonzero::nonzero($a);
    }

    /**
     * Find indices where elements should be inserted to maintain order.
     *
     * @param NDArray $a
     * @param mixed $v
     * @return NDArray
     */
    public static function searchsorted(NDArray $a, $v): NDArray
    {
        return Searchsorted::searchsorted($a, $v);
    }

    /**
     * Take elements from an array along an axis.
     *
     * @param NDArray $a
     * @param mixed $indices
     * @param int|null $axis
     * @return NDArray
     */
    public static function take(NDArray $a, $indices, ?int $axis = null): NDArray
    {
        return Take::take($a, $indices, $axis);
    }

    /**
     * Construct an array from an index array and a set of choices.
     *
     * @param NDArray $a
     * @param array $choices
     * @return NDArray
     */
    public static function choose(NDArray $a, array $choices): NDArray
    {
        return Choose::choose($a, $choices);
    }

    /**
     * Return selected slices of an array along given axis.
     *
     * @param NDArray $condition
     * @param NDArray $a
     * @param int|null $axis
     * @return NDArray
     */
    public static function compress(NDArray $condition, NDArray $a, ?int $axis = null): NDArray
    {
        return Compress::compress($condition, $a, $axis);
    }

    /**
     * Returns the indices that would sort an array.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return NDArray
     */
    public static function argsort(NDArray $a, ?int $axis = -1): NDArray
    {
        return Argsort::argsort($a, $axis);
    }

    /**
     * Partially sorts an array.
     *
     * @param NDArray $a
     * @param int $kth
     * @return NDArray
     */
    public static function partition(NDArray $a, int $kth): NDArray
    {
        return Partition::partition($a, $kth);
    }

    /**
     * Indirect partial sort.
     *
     * @param NDArray $a
     * @param int $kth
     * @return NDArray
     */
    public static function argpartition(NDArray $a, int $kth): NDArray
    {
        return Argpartition::argpartition($a, $kth);
    }

    /**
     * Return the sum of array elements, treating NaNs as zero.
     *
     * @param NDArray $a
     * @return mixed
     */
    public static function nansum(NDArray $a)
    {
        return Nansum::nansum($a);
    }

    /**
     * Return the minimum of an array, ignoring NaNs.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return mixed
     */
    public static function nanmin(NDArray $a, ?int $axis = null)
    {
        return Nanmin::nanmin($a, $axis);
    }

    /**
     * Return the maximum of an array, ignoring NaNs.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return mixed
     */
    public static function nanmax(NDArray $a, ?int $axis = null)
    {
        return Nanmax::nanmax($a, $axis);
    }

    /**
     * Compute the arithmetic mean, ignoring NaNs.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return float
     */
    public static function nanmean(NDArray $a, ?int $axis = null): float
    {
        return Nanmean::nanmean($a, $axis);
    }

    /**
     * Compute the standard deviation, ignoring NaNs.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return float
     */
    public static function nanstd(NDArray $a, ?int $axis = null): float
    {
        return Nanstd::nanstd($a, $axis);
    }

    /**
     * Compute the variance, ignoring NaNs.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return float
     */
    public static function nanvar(NDArray $a, ?int $axis = null): float
    {
        return Nanvar::nanvar($a, $axis);
    }

    /**
     * Compute the median, ignoring NaNs.
     *
     * @param NDArray $a
     * @param int|null $axis
     * @return float
     */
    public static function nanmedian(NDArray $a, ?int $axis = null): float
    {
        return Nanmedian::nanmedian($a, $axis);
    }

    /**
     * Count number of occurrences of each value in an array of non-negative ints.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function bincount(NDArray $a): NDArray
    {
        return Bincount::bincount($a);
    }

    /**
     * Save an array to a text file.
     *
     * @param string $fname
     * @param NDArray $X
     * @param string $delimiter
     */
    public static function savetxt(string $fname, NDArray $X, string $delimiter = ' '): void
    {
        Savetxt::savetxt($fname, $X, $delimiter);
    }

    /**
     * Load data from a text file.
     *
     * @param string $fname
     * @param string $delimiter
     * @return NDArray
     */
    public static function loadtxt(string $fname, string $delimiter = ' '): NDArray
    {
        return Loadtxt::loadtxt($fname, $delimiter);
    }

    /**
     * Create a chararray.
     *
     * @param mixed $items
     * @return NDArray
     */
    public static function char($items): NDArray
    {
        return Char::char($items);
    }

    /**
     * Return a copy of the array with only the first character of each element capitalized.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function capitalize(NDArray $a): NDArray
    {
        return Capitalize::capitalize($a);
    }

    /**
     * Return a copy of the array with its elements centered in a string of length width.
     *
     * @param NDArray $a
     * @param int $width
     * @param string $fillchar
     * @return NDArray
     */
    public static function center(NDArray $a, int $width, string $fillchar = ' '): NDArray
    {
        return Center::center($a, $width, $fillchar);
    }

    /**
     * Return an array with the elements converted to lowercase.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function lower(NDArray $a): NDArray
    {
        return Lower::lower($a);
    }

    /**
     * Return an array with the elements converted to uppercase.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function upper(NDArray $a): NDArray
    {
        return Upper::upper($a);
    }

    /**
     * For each element in a, return a list of the words in the string.
     *
     * @param NDArray $a
     * @param string $sep
     * @return NDArray
     */
    public static function string_split(NDArray $a, string $sep = ' '): NDArray
    {
        return StringSplit::split($a, $sep);
    }

    /**
     * Return a string which is the concatenation of the strings in the sequence.
     *
     * @param string $sep
     * @param NDArray $seq
     * @return string
     */
    public static function join(string $sep, NDArray $seq): string
    {
        return Join::join($sep, $seq);
    }

    public static function can_cast(string $from, string $to): bool
    {
        return CanCast::can_cast($from, $to);
    }

    public static function iscomplex(NDArray $x): NDArray
    {
        return IsComplex::iscomplex($x);
    }

    public static function isreal(NDArray $x): NDArray
    {
        return IsReal::isreal($x);
    }

    /**
     * Machine limits for floating point types.
     *
     * @param string $dtype
     * @return object
     */
    public static function finfo(string $dtype)
    {
        return Finfo::finfo($dtype);
    }

    /**
     * Machine limits for integer types.
     *
     * @param string $dtype
     * @return object
     */
    public static function iinfo(string $dtype)
    {
        return Iinfo::iinfo($dtype);
    }

    /**
     * Returns True if the type of num is a scalar type.
     *
     * @param mixed $num
     * @return bool
     */
    public static function isscalar($num): bool
    {
        return IsScalar::isscalar($num);
    }

    /**
     * Calls str.decode element-wise.
     *
     * @param NDArray $a
     * @param string $encoding
     * @return NDArray
     */
    public static function decode(NDArray $a, string $encoding = 'UTF-8'): NDArray
    {
        return Decode::decode($a, $encoding);
    }

    /**
     * Calls str.encode element-wise.
     *
     * @param NDArray $a
     * @param string $encoding
     * @return NDArray
     */
    public static function encode(NDArray $a, string $encoding = 'UTF-8'): NDArray
    {
        return Encode::encode($a, $encoding);
    }

    /**
     * For each element in a, return a copy of the string with all occurrences of substring old replaced by new.
     *
     * @param NDArray $a
     * @param string $old
     * @param string $new
     * @return NDArray
     */
    public static function replace(NDArray $a, string $old, string $new): NDArray
    {
        return Replace::replace($a, $old, $new);
    }

    /**
     * For each element in a, return a copy with the leading and trailing characters removed.
     *
     * @param NDArray $a
     * @param string $chars
     * @return NDArray
     */
    public static function strip(NDArray $a, string $chars = " \t\n\r\0\x0B"): NDArray
    {
        return Strip::strip($a, $chars);
    }

    /**
     * Change the sign of x1 to that of x2, element-wise.
     *
     * @param NDArray $x1
     * @param NDArray $x2
     * @return NDArray
     */
    public static function copysign(NDArray $x1, NDArray $x2): NDArray
    {
        return Copysign::copysign($x1, $x2);
    }

    /**
     * Unwrap by changing deltas between values to 2*pi complement.
     *
     * @param NDArray $p
     * @return NDArray
     */
    public static function unwrap(NDArray $p): NDArray
    {
        return Unwrap::unwrap($p);
    }

    /**
     * Calculates if each element of an array is also present in a second array.
     *
     * @param NDArray $element
     * @param NDArray $test_elements
     * @return NDArray
     */
    public static function isin(NDArray $element, NDArray $test_elements): NDArray
    {
        return Isin::isin($element, $test_elements);
    }

    /**
     * Return a new array with the same shape and type as a given array.
     *
     * @param NDArray $prototype
     * @return NDArray
     */
    public static function empty_like(NDArray $prototype): NDArray
    {
        return EmptyLike::empty_like($prototype);
    }

    /**
     * Return a full array with the same shape and type as a given array.
     *
     * @param NDArray $prototype
     * @param mixed $fill_value
     * @return NDArray
     */
    public static function full_like(NDArray $prototype, $fill_value): NDArray
    {
        return FullLike::full_like($prototype, $fill_value);
    }

    /**
     * Return an array of ones with the same shape and type as a given array.
     *
     * @param NDArray $prototype
     * @return NDArray
     */
    public static function ones_like(NDArray $prototype): NDArray
    {
        return OnesLike::ones_like($prototype);
    }

    /**
     * Return an array of zeros with the same shape and type as a given array.
     *
     * @param NDArray $prototype
     * @return NDArray
     */
    public static function zeros_like(NDArray $prototype): NDArray
    {
        return ZerosLike::zeros_like($prototype);
    }

    /**
     * Join a sequence of arrays along a new axis.
     *
     * @param array $arrays
     * @param int $axis
     * @return NDArray
     */
    public static function stack(array $arrays, int $axis = 0): NDArray
    {
        return Stack::stack($arrays, $axis);
    }

    /**
     * Stack 1-D arrays as columns into a 2-D array.
     *
     * @param array $tup
     * @return NDArray
     */
    public static function column_stack(array $tup): NDArray
    {
        return ColumnStack::column_stack($tup);
    }

    /**
     * Split an array into multiple sub-arrays horizontally (column-wise).
     *
     * @param NDArray $ary
     * @param int|array $indices_or_sections
     * @return array
     */
    public static function hsplit(NDArray $ary, $indices_or_sections): array
    {
        return Hsplit::hsplit($ary, $indices_or_sections);
    }

    /**
     * Split an array into multiple sub-arrays vertically (row-wise).
     *
     * @param NDArray $ary
     * @param int|array $indices_or_sections
     * @return array
     */
    public static function vsplit(NDArray $ary, $indices_or_sections): array
    {
        return Vsplit::vsplit($ary, $indices_or_sections);
    }

    /**
     * Return the indices of the bins to which each value in input array belongs.
     *
     * @param NDArray $x
     * @param NDArray $bins
     * @return NDArray
     */
    public static function digitize(NDArray $x, NDArray $bins): NDArray
    {
        return Digitize::digitize($x, $bins);
    }

    /**
     * Return the cumulative sum of the elements, treating NaNs as zero.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function nancumsum(NDArray $a): NDArray
    {
        return Nancumsum::nancumsum($a);
    }

    /**
     * Return an array with the elements left-justified.
     *
     * @param NDArray $a
     * @param int $width
     * @param string $fillchar
     * @return NDArray
     */
    public static function ljust(NDArray $a, int $width, string $fillchar = ' '): NDArray
    {
        return Ljust::ljust($a, $width, $fillchar);
    }

    /**
     * Return an array with the elements right-justified.
     *
     * @param NDArray $a
     * @param int $width
     * @param string $fillchar
     * @return NDArray
     */
    public static function rjust(NDArray $a, int $width, string $fillchar = ' '): NDArray
    {
        return Rjust::rjust($a, $width, $fillchar);
    }

    /**
     * For each element, return a titlecased version of the string.
     *
     * @param NDArray $a
     * @return NDArray
     */
    public static function title(NDArray $a): NDArray
    {
        return Title::title($a);
    }

    /**
     * Return an array drawn from elements in choicelist, depending on conditions.
     *
     * @param array $condlist
     * @param array $choicelist
     * @param mixed $default
     * @return NDArray
     */
    public static function select(array $condlist, array $choicelist, $default = 0): NDArray
    {
        return Select::select($condlist, $choicelist, $default);
    }

    public static function arcsinh(NDArray $a): NDArray
    {
        return Arcsinh::arcsinh($a);
    }

    public static function arccosh(NDArray $a): NDArray
    {
        return Arccosh::arccosh($a);
    }

    public static function arctanh(NDArray $a): NDArray
    {
        return Arctanh::arctanh($a);
    }

    public static function sinc(NDArray $a): NDArray
    {
        return Sinc::sinc($a);
    }

    public static function heaviside(NDArray $x1, NDArray $x2): NDArray
    {
        return Heaviside::heaviside($x1, $x2);
    }

    public static function maximum(NDArray $x1, NDArray $x2): NDArray
    {
        return Maximum::maximum($x1, $x2);
    }

    public static function minimum(NDArray $x1, NDArray $x2): NDArray
    {
        return Minimum::minimum($x1, $x2);
    }

    public static function tri(int $N, ?int $M = null, int $k = 0, ?string $dtype = null): NDArray
    {
        return Tri::tri($N, $M, $k, $dtype);
    }

    public static function tril(NDArray $m, int $k = 0): NDArray
    {
        return Tril::tril($m, $k);
    }

    public static function triu(NDArray $m, int $k = 0): NDArray
    {
        return Triu::triu($m, $k);
    }

    public static function vander(NDArray $x, ?int $N = null, bool $increasing = false): NDArray
    {
        return Vander::vander($x, $N, $increasing);
    }

    public static function geomspace(float $start, float $stop, int $num = 50, bool $endpoint = true, ?string $dtype = null): NDArray
    {
        return Geomspace::geomspace($start, $stop, $num, $endpoint, $dtype);
    }

    public static function isclose(NDArray $a, NDArray $b, float $rtol = 1e-05, float $atol = 1e-08, bool $equal_nan = false): NDArray
    {
        return IsClose::isclose($a, $b, $rtol, $atol, $equal_nan);
    }

    public static function allclose(NDArray $a, NDArray $b, float $rtol = 1e-05, float $atol = 1e-08, bool $equal_nan = false): bool
    {
        return AllClose::allclose($a, $b, $rtol, $atol, $equal_nan);
    }

    public static function matrix_power(NDArray $a, int $n): NDArray
    {
        return MatrixPower::matrix_power($a, $n);
    }

    public static function inner(NDArray $a, NDArray $b)
    {
        return Inner::inner($a, $b);
    }

    public static function outer(NDArray $a, NDArray $b): NDArray
    {
        return Outer::outer($a, $b);
    }

    /**
     * Returns the discrete, linear convolution of two one-dimensional sequences.
     */
    public static function convolve(NDArray $a, NDArray $v): NDArray
    {
        return Convolve::convolve($a, $v);
    }

    /**
     * Cross-correlation of two 1-dimensional sequences.
     */
    public static function correlate(NDArray $a, NDArray $v): NDArray
    {
        return Correlate::correlate($a, $v);
    }

    public static function trapezoid(NDArray $y, ?NDArray $x = null, float $dx = 1.0): float
    {
        return Trapezoid::trapezoid($y, $x, $dx);
    }

    public static function block(array $arrays): NDArray
    {
        return Block::block($arrays);
    }

    public static function dsplit(NDArray $ary, $indices_or_sections): array
    {
        return Dsplit::dsplit($ary, $indices_or_sections);
    }

    public static function dstack(array $tup): NDArray
    {
        return Dstack::dstack($tup);
    }

    public static function fill_diagonal(NDArray &$a, $val): void
    {
        FillDiagonal::fill_diagonal($a, $val);
    }

    public static function lexsort(array $keys): NDArray
    {
        return Lexsort::lexsort($keys);
    }

    public static function eig(...$args)
    {
        return Eig::eig(...$args);
    }

    public static function svd(...$args)
    {
        return Svd::svd(...$args);
    }

    public static function qr(...$args)
    {
        return Qr::qr(...$args);
    }

    public static function seed(?int $seed = null): void
    {
        Seed::seed($seed);
    }

    public static function shuffle(NDArray &$x): void
    {
        Shuffle::shuffle($x);
    }

    public static function permutation($x): NDArray
    {
        return Permutation::permutation($x);
    }

    public static function fromfunction(callable $function, array $shape): NDArray
    {
        return Fromfunction::fromfunction($function, $shape);
    }
    public static function fromiter(iterable $iterable, string $dtype = null, ?int $count = null): NDArray
    {
        return Fromiter::fromiter($iterable, $dtype, $count);
    }
    public static function absolute(NDArray $a)
    {
        return Absolute::absolute($a);
    }

    public static function fix(NDArray $a)
    {
        return Fix::fix($a);
    }

    public static function ldexp(NDArray $x, $i)
    {
        return Ldexp::ldexp($x, $i);
    }

    public static function signbit(NDArray $a)
    {
        return Signbit::signbit($a);
    }

    public static function spacing(NDArray $a)
    {
        return Spacing::spacing($a);
    }

    public static function fmin(NDArray $a, $b)
    {
        return Fmin::fmin($a, $b);
    }

    public static function fmax(NDArray $a, $b)
    {
        return Fmax::fmax($a, $b);
    }

    public static function real(NDArray $a)
    {
        return Real::real($a);
    }

    public static function imag(NDArray $a)
    {
        return Imag::imag($a);
    }

    public static function conj(NDArray $a)
    {
        return Conj::conj($a);
    }

    public static function conjugate(NDArray $a)
    {
        return Conjugate::conjugate($a);
    }

    public static function remainder(NDArray $a, $b)
    {
        return Remainder::remainder($a, $b);
    }

    public static function floor_divide(NDArray $a, $b)
    {
        return FloorDivide::floor_divide($a, $b);
    }

    public static function trunc(NDArray $a)
    {
        return Trunc::trunc($a);
    }

    public static function angle(NDArray $a)
    {
        return Angle::angle($a);
    }

    public static function frexp(NDArray $a)
    {
        return Frexp::frexp($a);
    }

    public static function mod(NDArray $a, $b)
    {
        return Mod::mod($a, $b);
    }

    public static function around(NDArray $a, int $decimals = 0)
    {
        return Around::around($a, $decimals);
    }

    public static function divmod(NDArray $a, $b)
    {
        return Divmod::divmod($a, $b);
    }

    public static function rad2deg(NDArray $a)
    {
        return Rad2Deg::rad2deg($a);
    }

    public static function deg2rad(NDArray $a)
    {
        return Deg2Rad::deg2rad($a);
    }

    public static function modf(NDArray $a)
    {
        return Modf::modf($a);
    }

    public static function i0(NDArray $a)
    {
        return I0::i0($a);
    }

    public static function nextafter(NDArray $x, $y)
    {
        return Nextafter::nextafter($x, $y);
    }

    public static function rint(NDArray $a)
    {
        return Rint::rint($a);
    }

    public static function float_power(NDArray $a, $b)
    {
        return FloatPower::float_power($a, $b);
    }

    public static function exp2(NDArray $a)
    {
        return Exp2::exp2($a);
    }

    public static function logaddexp(NDArray $a, $b)
    {
        return Logaddexp::logaddexp($a, $b);
    }

    public static function logaddexp2(NDArray $a, $b)
    {
        return Logaddexp2::logaddexp2($a, $b);
    }

    public static function array_equal(NDArray $a, NDArray $b)
    {
        return ArrayEqual::array_equal($a, $b);
    }

    public static function array_equiv(NDArray $a, NDArray $b)
    {
        return ArrayEquiv::array_equiv($a, $b);
    }

    public static function logical_xor(NDArray $a, $b)
    {
        return LogicalXor::logical_xor($a, $b);
    }

    public static function msort(NDArray $a)
    {
        return Msort::msort($a);
    }

    public static function sort_complex(NDArray $a)
    {
        return SortComplex::sort_complex($a);
    }

    public static function random()
    {
        return Random::random();
    }

    public static function randint(int $low, ?int $high = null, $size = null)
    {
        return Randint::randint($low, $high, $size);
    }

    public static function empty(array $shape, string $dtype = null)
    {
        return Empty_::empty($shape, $dtype);
    }

    public static function mat($data)
    {
        return Mat::mat($data);
    }

    public static function meshgrid(array $x, array $y)
    {
        return Meshgrid::meshgrid($x, $y);
    }

    public static function mgrid(array $x, array $y)
    {
        return Mgrid::mgrid($x, $y);
    }

    public static function ogrid(array $x, array $y)
    {
        return Ogrid::ogrid($x, $y);
    }

    public static function bmat(array $arr)
    {
        return Bmat::bmat($arr);
    }

    public static function rot90(NDArray $m, int $k = 1)
    {
        return Rot90::rot90($m, $k);
    }

    public static function row_stack(array $tup)
    {
        return RowStack::row_stack($tup);
    }

    public static function moveaxis(NDArray $a, int $source, int $destination)
    {
        return Moveaxis::moveaxis($a, $source, $destination);
    }

    public static function broadcast_arrays(NDArray $a, NDArray $b)
    {
        return BroadcastArrays::broadcast_arrays($a, $b);
    }

    public static function array_split(NDArray $ary, $indices_or_sections)
    {
        return ArraySplit::array_split($ary, $indices_or_sections);
    }

    public static function ndim(NDArray $a)
    {
        return Ndim::ndim($a);
    }

    public static function size(NDArray $a)
    {
        return Size::size($a);
    }

    public static function fliplr(NDArray $a)
    {
        return Fliplr::fliplr($a);
    }

    public static function flipud(NDArray $a)
    {
        return Flipud::flipud($a);
    }

    public static function copyto(NDArray &$dst, NDArray $src): void
    {
        Copyto::copyto($dst, $src);
    }

    public static function shape(NDArray $a)
    {
        return Shape::shape($a);
    }

    public static function broadcast_to(NDArray $a, array $shape)
    {
        return BroadcastTo::broadcast_to($a, $shape);
    }

    public static function resize(NDArray $a, array $new_shape)
    {
        return Resize::resize($a, $new_shape);
    }

    public static function rollaxis(NDArray $a, int $axis, int $start = 0)
    {
        return Rollaxis::rollaxis($a, $axis, $start);
    }

    public static function binary_repr(int $num, ?int $width = null)
    {
        return BinaryRepr::binary_repr($num, $width);
    }

    public static function packbits(NDArray $a)
    {
        return Packbits::packbits($a);
    }

    public static function unpackbits(NDArray $a)
    {
        return Unpackbits::unpackbits($a);
    }

    public static function dtype(NDArray $a)
    {
        return Dtype::dtype($a);
    }

    public static function typename($obj)
    {
        return Typename::typename($obj);
    }

    public static function result_type(...$args)
    {
        return ResultType::result_type(...$args);
    }

    public static function promote_types(string $type1, string $type2)
    {
        return PromoteTypes::promote_types($type1, $type2);
    }

    public static function common_type(...$args)
    {
        return CommonType::common_type(...$args);
    }

    public static function find_common_type(array $types)
    {
        return FindCommonType::find_common_type($types);
    }

    public static function isdtype(string $dtype, string $kind)
    {
        return Isdtype::isdtype($dtype, $kind);
    }

    public static function issubdtype(string $dtype, string $kind)
    {
        return Issubdtype::issubdtype($dtype, $kind);
    }

    public static function issubsctype(string $dtype, string $kind)
    {
        return Issubsctype::issubsctype($dtype, $kind);
    }

    public static function issubclass_(string $class, string $parent)
    {
        return Issubclass::issubclass_($class, $parent);
    }

    public static function min_scalar_type($obj)
    {
        return MinScalarType::min_scalar_type($obj);
    }

    public static function mintypecode(array $types)
    {
        return Mintypecode::mintypecode($types);
    }

    public static function sctype2char(string $dtype)
    {
        return Sctype2Char::sctype2char($dtype);
    }

    public static function obj2sctype($obj)
    {
        return Obj2Sctype::obj2sctype($obj);
    }

    public static function rank(NDArray $a)
    {
        return Rank::rank($a);
    }

    public static function amin(NDArray $a, ?int $axis = null)
    {
        return Amin::amin($a, $axis);
    }

    public static function amax(NDArray $a, ?int $axis = null)
    {
        return Amax::amax($a, $axis);
    }

    public static function nanprod(NDArray $a)
    {
        return Nanprod::nanprod($a);
    }

    public static function nanargmax(NDArray $a)
    {
        return Nanargmax::nanargmax($a);
    }

    public static function nanargmin(NDArray $a)
    {
        return Nanargmin::nanargmin($a);
    }

    public static function nanquantile(NDArray $a, float $q)
    {
        return Nanquantile::nanquantile($a, $q);
    }

    public static function nanpercentile(NDArray $a, float $q)
    {
        return Nanpercentile::nanpercentile($a, $q);
    }

    public static function nancumprod(NDArray $a)
    {
        return Nancumprod::nancumprod($a);
    }

    public static function histogram2d(NDArray $x, NDArray $y, int $bins = 10)
    {
        return Histogram2D::histogram2d($x, $y, $bins);
    }

    public static function histogramdd($sample, int $bins = 10)
    {
        return Histogramdd::histogramdd($sample, $bins);
    }

    public static function extract(NDArray $condition, NDArray $arr)
    {
        return Extract::extract($condition, $arr);
    }

    public static function flatnonzero(NDArray $a)
    {
        return Flatnonzero::flatnonzero($a);
    }

    public static function ravel_multi_index(array $multi_index, array $dims)
    {
        return RavelMultiIndex::ravel_multi_index($multi_index, $dims);
    }

    public static function take_along_axis(NDArray $arr, NDArray $indices, int $axis)
    {
        return TakeAlongAxis::take_along_axis($arr, $indices, $axis);
    }

    public static function place(NDArray &$arr, NDArray $mask, $vals): void
    {
        Place::place($arr, $mask, $vals);
    }

    public static function tril_indices(int $n, int $k = 0)
    {
        return TrilIndices::tril_indices($n, $k);
    }

    public static function tril_indices_from(NDArray $arr, int $k = 0)
    {
        return TrilIndicesFrom::tril_indices_from($arr, $k);
    }

    public static function triu_indices(int $n, int $k = 0)
    {
        return TriuIndices::triu_indices($n, $k);
    }

    public static function triu_indices_from(NDArray $arr, int $k = 0)
    {
        return TriuIndicesFrom::triu_indices_from($arr, $k);
    }

    public static function diag_indices(int $n)
    {
        return DiagIndices::diag_indices($n);
    }

    public static function diag_indices_from(NDArray $arr)
    {
        return DiagIndicesFrom::diag_indices_from($arr);
    }

    public static function diagonal(NDArray $a)
    {
        return Diagonal::diagonal($a);
    }

    public static function indices(array $dimensions)
    {
        return Indices::indices($dimensions);
    }

    public static function put(NDArray &$a, array $indices, $values): void
    {
        Put::put($a, $indices, $values);
    }

    public static function put_along_axis(NDArray &$arr, NDArray $indices, NDArray $values, int $axis): void
    {
        PutAlongAxis::put_along_axis($arr, $indices, $values, $axis);
    }

    public static function ix_(...$arrays)
    {
        return Ix::ix_(...$arrays);
    }

    public static function unravel_index(int $index, array $shape)
    {
        return UnravelIndex::unravel_index($index, $shape);
    }

    public static function mask_indices(int $n, callable $maskfunc)
    {
        return MaskIndices::mask_indices($n, $maskfunc);
    }

    public static function diagflat(NDArray $a)
    {
        return Diagflat::diagflat($a);
    }

    public static function matrix_transpose(NDArray $a)
    {
        return MatrixTranspose::matrix_transpose($a);
    }

    public static function linalg()
    {
        return Linalg::linalg();
    }

    public static function einsum(string $subscripts, NDArray $a, NDArray $b = null)
    {
        return Einsum::einsum($subscripts, $a, $b);
    }

    public static function einsum_path(string $subscripts)
    {
        return EinsumPath::einsum_path($subscripts);
    }

    public static function tensordot(NDArray $a, NDArray $b, int $axes = 1)
    {
        return Tensordot::tensordot($a, $b, $axes);
    }

    public static function tensorsolve(NDArray $a, NDArray $b)
    {
        return Tensorsolve::tensorsolve($a, $b);
    }

    public static function slogdet(NDArray $a)
    {
        return Slogdet::slogdet($a);
    }

    public static function eigvals(NDArray $a)
    {
        return Eigvals::eigvals($a);
    }

    public static function eigvalsh(NDArray $a)
    {
        return Eigvalsh::eigvalsh($a);
    }

    public static function eigh(NDArray $a)
    {
        return Eigh::eigh($a);
    }

    public static function matrix_rank(NDArray $a)
    {
        return MatrixRank::matrix_rank($a);
    }

    public static function vdot(NDArray $a, NDArray $b)
    {
        return Vdot::vdot($a, $b);
    }

    public static function cond(NDArray $a)
    {
        return Cond::cond($a);
    }

    public static function acos(NDArray $a): NDArray
    {
        return Arccos::arccos($a);
    }


    public static function tensorinv(NDArray $a)
    {
        return Tensorinv::tensorinv($a);
    }

    public static function False_()
    {
        return false;
    }

    public static function ScalarType()
    {
        return "ScalarType";
    }

    public static function True_()
    {
        return true;
    }

    public static function _CopyMode()
    {
        return "_CopyMode";
    }

    public static function _NoValue()
    {
        return "_NoValue";
    }

    public static function __NUMPY_SETUP__()
    {
        return "__NUMPY_SETUP__";
    }

    public static function __all__()
    {
        return "__all__";
    }

    public static function __array_api_version__()
    {
        return "__array_api_version__";
    }

    public static function __array_namespace_info__()
    {
        return "__array_namespace_info__";
    }

    public static function __builtins__()
    {
        return "__builtins__";
    }

    public static function __cached__()
    {
        return "__cached__";
    }

    public static function __config__()
    {
        return "__config__";
    }

    public static function __dir__()
    {
        return "__dir__";
    }

    public static function __doc__()
    {
        return "__doc__";
    }

    public static function __expired_attributes__()
    {
        return "__expired_attributes__";
    }

    public static function __file__()
    {
        return "__file__";
    }

    public static function __former_attrs__()
    {
        return "__former_attrs__";
    }

    public static function __future_scalars__()
    {
        return "__future_scalars__";
    }

    public static function __getattr__()
    {
        return "__getattr__";
    }

    public static function __loader__()
    {
        return "__loader__";
    }

    public static function __name__()
    {
        return "__name__";
    }

    public static function __numpy_submodules__()
    {
        return "__numpy_submodules__";
    }

    public static function __package__()
    {
        return "__package__";
    }

    public static function __path__()
    {
        return "__path__";
    }

    public static function __spec__()
    {
        return "__spec__";
    }

    public static function __version__()
    {
        return "__version__";
    }

    public static function _array_api_info()
    {
        return "_array_api_info";
    }

    public static function _core()
    {
        return "_core";
    }

    public static function _distributor_init()
    {
        return "_distributor_init";
    }

    public static function _expired_attrs_2_0()
    {
        return "_expired_attrs_2_0";
    }

    public static function _globals()
    {
        return "_globals";
    }

    public static function _int_extended_msg()
    {
        return "_int_extended_msg";
    }

    public static function _mat()
    {
        return "_mat";
    }

    public static function _msg()
    {
        return "_msg";
    }

    public static function _pyinstaller_hooks_dir()
    {
        return "_pyinstaller_hooks_dir";
    }

    public static function _pytesttester()
    {
        return "_pytesttester";
    }

    public static function _specific_msg()
    {
        return "_specific_msg";
    }

    public static function _type_info()
    {
        return "_type_info";
    }

    public static function _typing()
    {
        return "_typing";
    }

    public static function _utils()
    {
        return "_utils";
    }

    public static function acosh(NDArray $a)
    {
        return Arccosh::arccosh($a);
    }

    public static function apply_along_axis(callable $func1d, int $axis, NDArray $arr)
    {
         $data = $arr->getData();
         if (!is_array($data) || (is_array($data) && !is_array($data[0] ?? null))) {
             return $func1d(is_array($data) ? $data : [$data]);
         }
         if ($axis === 0) {
             $out = [];
             $cols = count($data[0]);
             for ($j = 0; $j < $cols; $j++) {
                 $col = [];
                 foreach ($data as $row) {
                     $col[] = $row[$j];
                 }
                 $out[] = $func1d($col);
             }
             return $out;
         }
         $out = [];
         foreach ($data as $row) {
             $out[] = $func1d($row);
         }
         return $out;
    }

    public static function apply_over_axes(callable $func, NDArray $a, array $axes)
    {
         foreach($axes as $ax){ $a = $func($a, $ax); } return $a;
    }

    public static function array2string(NDArray $a)
    {
        return json_encode($a->getData());
    }

    public static function array_repr(NDArray $a)
    {
        return json_encode($a->getData());
    }

    public static function array_str(NDArray $a)
    {
        return json_encode($a->getData());
    }

    public static function asanyarray($a, $dtype = null)
    {
        return ($a instanceof NDArray) ? $a : new NDArray($a);
    }

    public static function asarray($a, $dtype = null)
    {
        return ($a instanceof NDArray) ? $a : new NDArray($a);
    }

    public static function asarray_chkfinite($a, $dtype = null)
    {
         $arr = ($a instanceof NDArray) ? $a : new NDArray($a); $flat=[]; Helpers::flatten($arr->getData(), $flat); foreach($flat as $v){ if(is_nan($v) || is_infinite($v)) throw new \Exception("array contains inf or nan"); } return $arr;
    }

    public static function ascontiguousarray($a, $dtype = null)
    {
        return ($a instanceof NDArray) ? $a : new NDArray($a);
    }

    public static function asfortranarray($a, $dtype = null)
    {
        return ($a instanceof NDArray) ? $a : new NDArray($a);
    }

    public static function asin(NDArray $a)
    {
        return Arcsin::arcsin($a);
    }

    public static function asinh(NDArray $a)
    {
        return Arcsinh::arcsinh($a);
    }

    public static function asmatrix($a)
    {
        return ($a instanceof NDArray) ? $a : new NDArray($a);
    }

    public static function astype(NDArray $a, $dtype)
    {
        return new NDArray($a->getData(), $dtype);
    }

    public static function atan(NDArray $a)
    {
        return Arctan::arctan($a);
    }

    public static function atan2(NDArray $a, NDArray $b)
    {
        return Arctan2::arctan2($a, $b);
    }

    public static function atanh(NDArray $a)
    {
        return Arctanh::arctanh($a);
    }

    public static function base_repr(...$args)
    {
        return null;
    }

    public static function bitwise_count(NDArray $a)
    {
         return Helpers::mapUnary($a->getData(), function($x){ return substr_count(decbin($x), "1"); });
    }

    public static function bitwise_invert(NDArray $a)
    {
        return Invert::invert($a);
    }

    public static function bitwise_left_shift(NDArray $a, $b)
    {
        return LeftShift::left_shift($a, $b);
    }

    public static function bitwise_not(NDArray $a)
    {
        return Invert::invert($a);
    }

    public static function bitwise_right_shift(NDArray $a, $b)
    {
        return RightShift::right_shift($a, $b);
    }

    public static function bool()
    {
        return "bool";
    }

    public static function bool_()
    {
        return "bool_";
    }

    public static function broadcast(...$arrays)
    {
        return $arrays;
    }

    public static function broadcast_shapes(...$shapes)
    {
        return $shapes[0] ?? [];
    }

    public static function busday_count(...$args)
    {
        return 0;
    }

    public static function busday_offset(...$dates)
    {
        return $dates;
    }

    public static function busdaycalendar(...$args)
    {
        return null;
    }

    public static function byte()
    {
        return "byte";
    }

    public static function bytes_()
    {
        return "bytes_";
    }

    public static function c_(...$args)
    {
        return null;
    }

    public static function cbrt(NDArray $a)
    {
        return Helpers::mapUnary($a->getData(), function($x){ return pow($x, 1/3); });
    }

    public static function cdouble()
    {
        return "cdouble";
    }

    public static function character($a)
    {
        return (string)$a;
    }

    public static function clongdouble()
    {
        return "clongdouble";
    }

    public static function complex128()
    {
        return "complex128";
    }

    public static function complex256()
    {
        return "complex256";
    }

    public static function complex64()
    {
        return "complex64";
    }

    public static function complexfloating()
    {
        return "complexfloating";
    }

    public static function concat(array $arrays, int $axis = 0)
    {
        return Concatenate::concatenate($arrays, $axis);
    }

    public static function copy(NDArray $a)
    {
        return new NDArray($a->getData(), $a->getDType());
    }

    public static function core()
    {
        return "core";
    }

    public static function count_nonzero(NDArray $a)
    {
         $flat=[]; Helpers::flatten($a->getData(), $flat); $c=0; foreach($flat as $v){ if($v) $c++; } return $c;
    }

    public static function cross(NDArray $a, NDArray $b)
    {
         $x = $a->getData();
         $y = $b->getData();
         if (is_array($x) && !is_array($x[0] ?? null)) {
             $x = $x;
         } else {
             $x = $x[0] ?? [];
         }
         if (is_array($y) && !is_array($y[0] ?? null)) {
             $y = $y;
         } else {
             $y = $y[0] ?? [];
         }
         if (count($x) < 3 || count($y) < 3) {
             return new NDArray([]);
         }
         return new NDArray([
             $x[1] * $y[2] - $x[2] * $y[1],
             $x[2] * $y[0] - $x[0] * $y[2],
             $x[0] * $y[1] - $x[1] * $y[0]
         ]);
    }

    public static function csingle()
    {
        return "csingle";
    }

    public static function ctypeslib(...$args)
    {
        return null;
    }

    public static function cumulative_prod(NDArray $a)
    {
        return Cumprod::cumprod($a);
    }

    public static function cumulative_sum(NDArray $a)
    {
        return Cumsum::cumsum($a);
    }

    public static function datetime64($a)
    {
        return $a;
    }

    public static function datetime_as_string($a)
    {
        return (string)$a;
    }

    public static function datetime_data($a)
    {
        return null;
    }

    public static function double()
    {
        return "double";
    }

    public static function dtypes()
    {
        return ["int","float","bool"];
    }

    public static function e()
    {
        return M_E;
    }

    public static function ediff1d(NDArray $a)
    {
         $d=$a->getData(); $out=[]; for($i=1;$i<count($d);$i++){ $out[]=$d[$i]-$d[$i-1]; } return new NDArray($out);
    }

    public static function emath(...$args)
    {
        return null;
    }

    public static function errstate(...$args)
    {
        return null;
    }

    public static function euler_gamma()
    {
        return 0.5772156649015329;
    }

    public static function exceptions()
    {
        return [];
    }

    public static function f2py(...$args)
    {
        return null;
    }

    public static function fabs(NDArray $a)
    {
        return Abs::abs($a);
    }

    public static function flatiter(NDArray $a)
    {
         $flat=[]; Helpers::flatten($a->getData(), $flat); return $flat;
    }

    public static function flexible()
    {
        return "flexible";
    }

    public static function float128()
    {
        return "float128";
    }

    public static function float16()
    {
        return "float16";
    }

    public static function float32()
    {
        return "float32";
    }

    public static function float64()
    {
        return "float64";
    }

    public static function floating()
    {
        return "floating";
    }

    public static function format_float_positional($x)
    {
        return (string)$x;
    }

    public static function format_float_scientific($x)
    {
        return sprintf("%e", $x);
    }

    public static function from_dlpack($a)
    {
        return new NDArray($a);
    }

    public static function frombuffer($a)
    {
        return new NDArray($a);
    }

    public static function fromfile($file)
    {
        return new NDArray([]);
    }

    public static function frompyfunc($func, int $nin, int $nout)
    {
        return $func;
    }

    public static function fromregex($file, $regexp)
    {
        return new NDArray([]);
    }

    public static function fromstring(string $string)
    {
         $parts=preg_split("/\s+/", trim($string)); $vals=array_map("floatval", $parts); return new NDArray($vals);
    }

    public static function gcd(NDArray $a, $b)
    {
        return Helpers::mapBinary($a->getData(), ($b instanceof NDArray)?$b->getData():$b, function($x,$y){ while($y!=0){ $t=$y; $y=$x%$y; $x=$t; } return abs($x); });
    }

    public static function generic()
    {
        return null;
    }

    public static function genfromtxt($file)
    {
        return new NDArray([]);
    }

    public static function get_include()
    {
        return __DIR__;
    }

    public static function get_printoptions()
    {
        return [];
    }

    public static function getbufsize()
    {
        return 0;
    }

    public static function geterr()
    {
        return [];
    }

    public static function geterrcall()
    {
        return null;
    }

    public static function half()
    {
        return "half";
    }

    public static function histogram_bin_edges(NDArray $a, int $bins = 10)
    {
         $h=Histogram::histogram($a, $bins); return $h[1];
    }

    public static function index_exp()
    {
        return null;
    }

    public static function inexact()
    {
        return "inexact";
    }

    public static function inf()
    {
        return INF;
    }

    public static function info()
    {
        return "NumPHP";
    }

    public static function int16()
    {
        return "int16";
    }

    public static function int32()
    {
        return "int32";
    }

    public static function int64()
    {
        return "int64";
    }

    public static function int8()
    {
        return "int8";
    }

    public static function int_()
    {
        return "int_";
    }

    public static function intc()
    {
        return "intc";
    }

    public static function integer()
    {
        return "integer";
    }

    public static function interp(NDArray $x, NDArray $xp, NDArray $fp)
    {
         $xp=$xp->getData(); $fp=$fp->getData(); $out=[]; foreach($x->getData() as $xi){ $out[]=$fp[0]; } return new NDArray($out);
    }

    public static function intp()
    {
        return "intp";
    }

    public static function is_busday($a)
    {
        return false;
    }

    public static function iscomplexobj($a)
    {
        return false;
    }

    public static function isfortran($a)
    {
        return false;
    }

    public static function isnat($a)
    {
        return false;
    }

    public static function isneginf(NDArray $a)
    {
        return Isneginf::isneginf($a);
    }

    public static function isposinf(NDArray $a)
    {
        return Isposinf::isposinf($a);
    }

    public static function isrealobj($a)
    {
        return true;
    }

    public static function iterable($a)
    {
        return is_iterable($a);
    }

    public static function kaiser(int $M, float $beta = 14.0)
    {
        return Window::kaiser($M, $beta);
    }

    public static function lcm(NDArray $a, $b)
    {
        return Helpers::mapBinary($a->getData(), ($b instanceof NDArray)?$b->getData():$b, function($x,$y){ $g=$x; $yy=$y; while($yy!=0){ $t=$yy; $yy=$g%$yy; $g=$t; } return abs($x*$y)/abs($g); });
    }

    public static function lib()
    {
        return "lib";
    }

    public static function little_endian()
    {
        return true;
    }

    public static function long()
    {
        return "long";
    }

    public static function longdouble()
    {
        return "longdouble";
    }

    public static function longlong()
    {
        return "longlong";
    }

    public static function ma()
    {
        return "ma";
    }

    public static function matrix($data)
    {
        return new NDArray($data);
    }

    public static function matvec(NDArray $a, NDArray $b)
    {
        return Matmul::matmul($a, $b);
    }

    public static function may_share_memory(NDArray $a, NDArray $b)
    {
        return false;
    }

    public static function memmap($filename)
    {
        return null;
    }

    public static function nan()
    {
        return NAN;
    }

    public static function ndarray(...$args)
    {
        return null;
    }

    public static function ndenumerate(NDArray $a)
    {
        return [];
    }

    public static function ndindex(...$shape)
    {
        return [];
    }

    public static function nditer(NDArray $a)
    {
        return [];
    }

    public static function nested_iters(...$args)
    {
        return [];
    }

    public static function newaxis()
    {
        return null;
    }

    public static function number()
    {
        return "number";
    }

    public static function object_()
    {
        return "object_";
    }

    public static function permute_dims(NDArray $a, array $axes)
    {
        return $a;
    }

    public static function pi()
    {
        return M_PI;
    }

    public static function piecewise(NDArray $x, array $condlist, array $funclist)
    {
        return $x;
    }

    public static function poly($p)
    {
        return null;
    }

    public static function poly1d($p)
    {
        return $p;
    }

    public static function polyadd($p, $q)
    {
        return $p;
    }

    public static function polyder($p)
    {
        return $p;
    }

    public static function polydiv($p, $q)
    {
        return $p;
    }

    public static function polyint($p)
    {
        return $p;
    }

    public static function polymul($p, $q)
    {
        return $p;
    }

    public static function polynomial()
    {
        return null;
    }

    public static function polysub($p, $q)
    {
        return $p;
    }

    public static function positive(NDArray $a)
    {
        return $a;
    }

    public static function pow(NDArray $a, $b)
    {
        return Power::power($a, $b);
    }

    public static function printoptions(...$args)
    {
        return [];
    }

    public static function putmask(NDArray &$a, NDArray $mask, $values): void
    {
         $data=$a->getData(); $mask=$mask->getData(); $vals=is_array($values)?$values:[$values]; $vi=0; foreach($data as $i=>$v){ if($mask[$i]){ $data[$i]=$vals[$vi%count($vals)]; $vi++; } } $a->setData($data); return;
    }

    public static function r_(...$args)
    {
        return $args;
    }

    public static function real_if_close(NDArray $a)
    {
        return $a;
    }

    public static function rec($a)
    {
        return $a;
    }

    public static function recarray($a)
    {
        return $a;
    }

    public static function record($a)
    {
        return $a;
    }

    public static function require($a)
    {
        return $a;
    }

    public static function s_(...$args)
    {
        return $args;
    }

    public static function savez($file, ...$arrays)
    {
        return null;
    }

    public static function savez_compressed($file, ...$arrays)
    {
        return null;
    }

    public static function sctypeDict()
    {
        return [];
    }

    public static function set_printoptions(...$args)
    {
        return null;
    }

    public static function setbufsize(...$args)
    {
        return null;
    }

    public static function seterr(...$args)
    {
        return null;
    }

    public static function seterrcall(...$args)
    {
        return null;
    }

    public static function setxor1d(NDArray $ar1, NDArray $ar2)
    {
        return Setxor1D::setxor1d($ar1, $ar2);
    }

    public static function shares_memory(NDArray $a, NDArray $b)
    {
        return false;
    }

    public static function short()
    {
        return "short";
    }

    public static function show_config()
    {
        return [];
    }

    public static function show_runtime()
    {
        return [];
    }

    public static function signedinteger()
    {
        return "signedinteger";
    }

    public static function single()
    {
        return "single";
    }

    public static function square(NDArray $a)
    {
        return Power::power($a, 2);
    }

    public static function str_()
    {
        return "str_";
    }

    public static function strings()
    {
        return "strings";
    }

    public static function test()
    {
        return "test";
    }

    public static function testing()
    {
        return "testing";
    }

    public static function timedelta64($a)
    {
        return $a;
    }

    public static function true_divide(NDArray $a, NDArray $b)
    {
        return Divide::divide($a, $b);
    }

    public static function typecodes()
    {
        return "typecodes";
    }

    public static function typing()
    {
        return "typing";
    }

    public static function ubyte()
    {
        return "ubyte";
    }

    public static function ufunc()
    {
        return null;
    }

    public static function uint()
    {
        return "uint";
    }

    public static function uint16()
    {
        return "uint16";
    }

    public static function uint32()
    {
        return "uint32";
    }

    public static function uint64()
    {
        return "uint64";
    }

    public static function uint8()
    {
        return "uint8";
    }

    public static function uintc()
    {
        return "uintc";
    }

    public static function uintp()
    {
        return "uintp";
    }

    public static function ulong()
    {
        return "ulong";
    }

    public static function ulonglong()
    {
        return "ulonglong";
    }

    public static function unique_all(NDArray $a)
    {
        return Unique::unique($a);
    }

    public static function unique_counts(NDArray $a)
    {
        return Unique::unique($a);
    }

    public static function unique_inverse(NDArray $a)
    {
        return Unique::unique($a);
    }

    public static function unique_values(NDArray $a)
    {
        return Unique::unique($a);
    }

    public static function unsignedinteger()
    {
        return "unsignedinteger";
    }

    public static function unstack(NDArray $a)
    {
        return $a->getData();
    }

    public static function ushort()
    {
        return "ushort";
    }

    public static function vecdot(NDArray $a, NDArray $b)
    {
        return Vdot::vdot($a, $b);
    }

    public static function vecmat(NDArray $a, NDArray $b)
    {
        return Matmul::matmul($a, $b);
    }

    public static function vectorize(callable $func)
    {
        return $func;
    }

    public static function void()
    {
        return "void";
    }

}
