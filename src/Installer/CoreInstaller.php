<?php
/**
 * The MIT License (MIT)
 * Copyright © 2014 FancyGuy Technologies
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the “Software”), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace FancyGuy\Composer\WordPressInstaller\Installer;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

class CoreInstaller extends LibraryInstaller {

    const INSTALLER_TYPE = 'wordpress-core';

    /**
     * {@inheritDoc}
     */
    public function getPackageBasePath(PackageInterface $package) {
        if ($this->composer->getPackage()) {
            $extra = $this->composer->getPackage()->getExtra();

            if (!empty($extra['wordpress']) && !empty($extra['wordpress']['core-path'])) {
                return $extra['wordpress']['core-path'];
            } else {
                throw new \InvalidArgumentException('In order to install wordpress core you need to configure the core path.');
            }
        } else {
            throw new \InvalidArgumentException('The root package is not configured properly.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType) {
        return self::INSTALLER_TYPE === $packageType;
    }

}
