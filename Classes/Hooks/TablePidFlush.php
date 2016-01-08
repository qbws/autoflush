<?php
namespace Qbus\Autoflush\Hooks;

use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TablePidFlush
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TablePidFlush
{
    /**
     * Clear table_pid_PID when a record is changed
     *
     * While the DataHandler already clears tableName and tableName_UID
     * tableName_pid_PID clearing is still missing.
     *
     * Usecase: shared content that selects content from a pid other than the current
     * lib.sharedContent = CONTENT
     * lib.sharedContent {
     *     table = tt_content
     *     select.where = colPos=0
     *     select.orderBy = sorting
     *     select.pidInList = 30
     *     # Add cache tag to clear the current page when content on page uid=30 changes
     *     stdWrap.addPageCacheTags = tt_content_pid_30
     * }
     *
     * TODO: Support move from one pid to another
     *
     * @param  array       $params
     * @param  DataHandler $dataHandler
     * @return void
     */
    public function clearCachePostProc(array $params, DataHandler $dataHandler)
    {
        if (isset($params['table']) && isset($params['uid_page'])) {
            $tag = $params['table'] . '_pid_' . $params['uid_page'];
            $this->getCacheManager()->flushCachesInGroupByTag('pages', $tag);
        }
    }

    /**
     * @return \TYPO3\CMS\Core\Cache\CacheManager;
     */
    protected function getCacheManager()
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Core\Cache\CacheManager::class);
    }
}
