<?php
/**
 * Refresh file headers from metadata.
 *
 * Usage: php refreshFileHeaders.php
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Maintenance
 */

use MediaWiki\FileRepo\File\FileSelectQueryBuilder;
use MediaWiki\FileRepo\LocalRepo;
use MediaWiki\Maintenance\Maintenance;
use Wikimedia\Rdbms\SelectQueryBuilder;

// @codeCoverageIgnoreStart
require_once __DIR__ . '/Maintenance.php';
// @codeCoverageIgnoreEnd

/**
 * Maintenance script to refresh file headers from metadata
 *
 * @ingroup Maintenance
 */
class RefreshFileHeaders extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Script to update file HTTP headers' );
		$this->addOption( 'verbose', 'Output information about each file.', false, false, 'v' );
		$this->addOption( 'start', 'Name of file to start with', false, true );
		$this->addOption( 'end', 'Name of file to end with', false, true );
		$this->addOption( 'media_type', 'Media type to filter for', false, true );
		$this->addOption( 'major_mime', 'Major mime type to filter for', false, true );
		$this->addOption( 'minor_mime', 'Minor mime type to filter for', false, true );
		$this->addOption(
			'refreshContentType',
			'Set true to refresh file content type from mime data in db',
			false,
			false
		);
		$this->setBatchSize( 200 );
	}

	public function execute() {
		$repo = $this->getServiceContainer()->getRepoGroup()->getLocalRepo();
		$start = str_replace( ' ', '_', $this->getOption( 'start', '' ) ); // page on img_name
		$end = str_replace( ' ', '_', $this->getOption( 'end', '' ) ); // page on img_name
		// filter by img_media_type
		$media_type = str_replace( ' ', '_', $this->getOption( 'media_type', '' ) );
		// filter by img_major_mime
		$major_mime = str_replace( ' ', '_', $this->getOption( 'major_mime', '' ) );
		// filter by img_minor_mime
		$minor_mime = str_replace( ' ', '_', $this->getOption( 'minor_mime', '' ) );

		$count = 0;
		$dbr = $this->getReplicaDB();

		do {
			$queryBuilder = FileSelectQueryBuilder::newForFile( $dbr );

			$queryBuilder->where( $dbr->expr( 'img_name', '>', $start ) );
			if ( $end !== '' ) {
				$queryBuilder->andWhere( $dbr->expr( 'img_name', '<=', $end ) );
			}

			if ( $media_type !== '' ) {
				$queryBuilder->andWhere( [ 'img_media_type' => $media_type ] );
			}

			if ( $major_mime !== '' ) {
				$queryBuilder->andWhere( [ 'img_major_mime' => $major_mime ] );
			}

			if ( $minor_mime !== '' ) {
				$queryBuilder->andWhere( [ 'img_minor_mime' => $minor_mime ] );
			}

			$res = $queryBuilder
				->orderBy( 'img_name', SelectQueryBuilder::SORT_ASC )
				->limit( $this->getBatchSize() )
				->caller( __METHOD__ )->fetchResultSet();

			if ( $res->numRows() > 0 ) {
				$row1 = $res->current();
				$this->output( "Processing next {$res->numRows()} row(s) starting with {$row1->img_name}.\n" );
				$res->rewind();
			}

			$backendOperations = [];

			foreach ( $res as $row ) {
				$file = $repo->newFileFromRow( $row );
				$headers = $file->getContentHeaders();
				if ( $this->getOption( 'refreshContentType', false ) ) {
					$headers['Content-Type'] = $row->img_major_mime . '/' . $row->img_minor_mime;
				}

				if ( count( $headers ) ) {
					$backendOperations[] = [
						'op' => 'describe', 'src' => $file->getPath(), 'headers' => $headers
					];
				}

				// Do all of the older file versions...
				foreach ( $file->getHistory() as $oldFile ) {
					$headers = $oldFile->getContentHeaders();
					if ( count( $headers ) ) {
						$backendOperations[] = [
							'op' => 'describe', 'src' => $oldFile->getPath(), 'headers' => $headers
						];
					}
				}

				if ( $this->hasOption( 'verbose' ) ) {
					$this->output( "Queued headers update for file '{$row->img_name}'.\n" );
				}

				$start = $row->img_name; // advance
			}

			$backendOperationsCount = count( $backendOperations );
			$count += $backendOperationsCount;

			$this->output( "Updating headers for {$backendOperationsCount} file(s).\n" );
			$this->updateFileHeaders( $repo, $backendOperations );
		} while ( $res->numRows() === $this->getBatchSize() );

		$this->output( "Done. Updated headers for $count file(s).\n" );
	}

	/**
	 * @param LocalRepo $repo
	 * @param array $backendOperations
	 */
	protected function updateFileHeaders( $repo, $backendOperations ) {
		$status = $repo->getBackend()->doQuickOperations( $backendOperations );

		if ( !$status->isGood() ) {
			$this->error( $status );
		}
	}
}

// @codeCoverageIgnoreStart
$maintClass = RefreshFileHeaders::class;
require_once RUN_MAINTENANCE_IF_MAIN;
// @codeCoverageIgnoreEnd
