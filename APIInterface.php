<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brs\SocialMediaAPI;

use Closure;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 */
interface APIInterface
{
    public function importPosts(Closure $importPostFn, array $options = []);
}