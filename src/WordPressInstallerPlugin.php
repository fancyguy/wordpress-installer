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

namespace FancyGuy\Composer\WordPressInstaller;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\PackageEvent;
use Composer\Script\ScriptEvents;
use Symfony\Component\Filesystem\Filesystem;

class WordPressInstallerPlugin implements PluginInterface, EventSubscriberInterface {

    public function activate(Composer $composer, IOInterface $io) {
        $composer->getInstallationManager()->addInstaller(new Installer\CoreInstaller($io, $composer));
     }

    public static function getSubscribedEvents() {
        return array(
            ScriptEvents::POST_PACKAGE_INSTALL => [
                'applyOverlay',
            ],
            ScriptEvents::POST_PACKAGE_UPDATE => [
                'applyOverlay',
            ],
        );
    }

    public function applyOverlay(PackageEvent $packageEvent) {
        switch($packageEvent->getOperation()->getJobType()) {
            case 'install':
                $package = $packageEvent->getOperation()->getPackage();
                break;
            case 'update':
                $package = $packageEvent->getOperation()-getTargetPackage();
                break;
            default:
                return;
        }

        if (Installer\CoreInstaller::INSTALLER_TYPE === $package->getType()) {
            $extra = $packageEvent->getComposer()->getPackage()->getExtra();
            if (empty($extra['wordpress'])) {
                return;
            }
            $config = $extra['wordpress'];
            if (!empty($config['core-path']) && !empty($config['overlay-path'])) {
                $filesystem = new Filesystem();

                $corePath = $packageEvent->getComposer()->getInstallationManager()->getInstaller($package->getType())->getInstallPath($package);
                $overlayPath = $config['overlay-path'];

                $filesystem->mirror($overlayPath, $corePath, null, array('override' => true));
            }
        }
    }

}
