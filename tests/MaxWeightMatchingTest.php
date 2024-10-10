<?php

namespace Bakert\BlossomPhp\Tests;

use Bakert\BlossomPhp\MaxWeightMatching;
use PHPUnit\Framework\TestCase;

class MaxWeightMatchingTest extends TestCase
{
    public function test10_empty() {
        # empty input graph
        $this->assertTrue($this->maxWeightMatching([]) === []);
    }

    public function test11_singleedge() {
        # single edge
        $this->assertTrue($this->maxWeightMatching([[0, 1, 1]]) === [1, 0]);
    }

    public function test12() {
        $this->assertTrue($this->maxWeightMatching([[1, 2, 10], [2, 3, 11]]) === [-1, -1, 3, 2]);
    }

    public function test13() {
        $this->assertTrue($this->maxWeightMatching([[1, 2, 5], [2, 3, 11], [3, 4, 5]]) === [-1, -1, 3, 2, -1]);
    }


    public function test14_maxcard() {
        # maximum cardinality
        $this->assertTrue($this->maxWeightMatching([[1, 2, 5], [2, 3, 11], [3, 4, 5]], true) === [-1, 2, 1, 4, 3]);
    }

    public function test15_float() {
        # floating point weigths
        $this->assertTrue($this->maxWeightMatching([[1, 2, M_PI], [2, 3, exp(1)], [1, 3, 3.0], [1, 4, sqrt(2.0)]]) === [-1, 4, 3, 2, 1]);
    }

    public function test16_negative() {
        # negative weights
        $this->assertTrue($this->maxWeightMatching([[1, 2, 2], [1, 3, -2], [2, 3, 1], [2, 4, -1], [3, 4, -6]], false) === [-1, 2, 1, -1, -1]);
        $this->assertTrue($this->maxWeightMatching([[1, 2, 2], [1, 3, -2], [2, 3, 1], [2, 4, -1], [3, 4, -6]], true) === [-1, 3, 4, 1, 2]);
    }


    public function test20_sblossom() {
        # create S-blossom && use it for augmentation
        $this->assertTrue($this->maxWeightMatching([[1, 2, 8], [1, 3, 9], [2, 3, 10], [3, 4, 7]]) === [-1, 2, 1, 4, 3]);
        $this->assertTrue($this->maxWeightMatching([[1, 2, 8], [1, 3, 9], [2, 3, 10], [3, 4, 7], [1, 6, 5], [4, 5, 6]]) === [-1, 6, 3, 2, 5, 4, 1]);
    }

    public function test21_tblossom() {
        # create S-blossom, relabel as T-blossom, use for augmentation
        $this->assertTrue($this->maxWeightMatching([[1, 2, 9], [1, 3, 8], [2, 3, 10], [1, 4, 5], [4, 5, 4], [1, 6, 3]]) === [-1, 6, 3, 2, 5, 4, 1]);
        $this->assertTrue($this->maxWeightMatching([[1, 2, 9], [1, 3, 8], [2, 3, 10], [1, 4, 5], [4, 5, 3], [1, 6, 4]]) === [-1, 6, 3, 2, 5, 4, 1]);
        $this->assertTrue($this->maxWeightMatching([[1, 2, 9], [1, 3, 8], [2, 3, 10], [1, 4, 5], [4, 5, 3], [3, 6, 4]]) === [-1, 2, 1, 6, 5, 4, 3]);
    }

    public function test22_s_nest() {
        # create nested S-blossom, use for augmentation
        $this->assertTrue($this->maxWeightMatching([[1, 2, 9], [1, 3, 9], [2, 3, 10], [2, 4, 8], [3, 5, 8], [4, 5, 10], [5, 6, 6]]) === [-1, 3, 4, 1, 2, 6, 5]);
    }

    public function test23_s_relabel_nest() {
        # create S-blossom, relabel as S, include in nested S-blossom
        $this->assertTrue($this->maxWeightMatching([[1, 2, 10], [1, 7, 10], [2, 3, 12], [3, 4, 20], [3, 5, 20], [4, 5, 25], [5, 6, 10], [6, 7, 10], [7, 8, 8]]) === [-1, 2, 1, 4, 3, 6, 5, 8, 7]);
    }

    public function test24_s_nest_expand() {
        # create nested S-blossom, augment, expand recursively
        $this->assertTrue($this->maxWeightMatching([[1, 2, 8], [1, 3, 8], [2, 3, 10], [2, 4, 12], [3, 5, 12], [4, 5, 14], [4, 6, 12], [5, 7, 12], [6, 7, 14], [7, 8, 12]]) === [-1, 2, 1, 5, 6, 3, 4, 8, 7]);
    }

    public function test25_s_t_expand() {
        # create S-blossom, relabel as T, expand
        $this->assertTrue($this->maxWeightMatching([[1, 2, 23], [1, 5, 22], [1, 6, 15], [2, 3, 25], [3, 4, 22], [4, 5, 25], [4, 8, 14], [5, 7, 13]]) === [-1, 6, 3, 2, 8, 7, 1, 5, 4]);
    }

    public function test26_s_nest_t_expand() {
        # create nested S-blossom, relabel as T, expand
        $this->assertTrue($this->maxWeightMatching([[1, 2, 19], [1, 3, 20], [1, 8, 8], [2, 3, 25], [2, 4, 18], [3, 5, 18], [4, 5, 13], [4, 7, 7], [5, 6, 7]]) === [-1, 8, 3, 2, 7, 6, 5, 4, 1]);
    }

    public function test30_tnasty_expand() {
        # create blossom, relabel as T in more than one way, expand, augment
        $this->assertTrue($this->maxWeightMatching([[1, 2, 45], [1, 5, 45], [2, 3, 50], [3, 4, 45], [4, 5, 50], [1, 6, 30], [3, 9, 35], [4, 8, 35], [5, 7, 26], [9, 10, 5]]) === [-1, 6, 3, 2, 8, 7, 1, 5, 4, 10, 9]);
    }

    public function test31_tnasty2_expand() {
        # again but slightly different
        $this->assertTrue($this->maxWeightMatching([[1, 2, 45], [1, 5, 45], [2, 3, 50], [3, 4, 45], [4, 5, 50], [1, 6, 30], [3, 9, 35], [4, 8, 26], [5, 7, 40], [9, 10, 5]]) === [-1, 6, 3, 2, 8, 7, 1, 5, 4, 10, 9]);
    }

    public function test32_t_expand_leastslack() {
        # create blossom, relabel as T, expand such that a new least-slack S-to-free edge is produced, augment
        $this->assertTrue($this->maxWeightMatching([[1, 2, 45], [1, 5, 45], [2, 3, 50], [3, 4, 45], [4, 5, 50], [1, 6, 30], [3, 9, 35], [4, 8, 28], [5, 7, 26], [9, 10, 5]]) === [-1, 6, 3, 2, 8, 7, 1, 5, 4, 10, 9]);
    }

    public function test33_nest_tnasty_expand() {
        # create nested blossom, relabel as T in more than one way, expand outer blossom such that inner blossom ends up on an augmenting $path
        $this->assertTrue($this->maxWeightMatching([[1, 2, 45], [1, 7, 45], [2, 3, 50], [3, 4, 45], [4, 5, 95], [4, 6, 94], [5, 6, 94], [6, 7, 50], [1, 8, 30], [3, 11, 35], [5, 9, 36], [7, 10, 26], [11, 12, 5]]) === [-1, 8, 3, 2, 6, 9, 4, 10, 1, 5, 7, 12, 11]);
    }

    public function test34_nest_relabel_expand() {
        # create nested S-blossom, relabel as S, expand recursively
        $this->assertTrue($this->maxWeightMatching([[1, 2, 40], [1, 3, 40], [2, 3, 60], [2, 4, 55], [3, 5, 55], [4, 5, 50], [1, 8, 15], [5, 7, 30], [7, 6, 10], [8, 10, 10], [4, 9, 30]]) === [-1, 2, 1, 5, 9, 3, 7, 6, 10, 4, 8]);
    }

    private function maxWeightMatching($edges, $maxcardinality=false) {
        $o = new MaxWeightMatching($edges, $maxcardinality, true);
        return $o->main();
    }
}
