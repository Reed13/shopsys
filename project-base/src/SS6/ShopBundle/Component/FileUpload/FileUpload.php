<?php

namespace SS6\ShopBundle\Component\FileUpload;

use SS6\ShopBundle\Component\String\TransformString;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUpload {

	const TEMPORARY_DIRECTORY = 'fileUploads';
	const UPLOAD_FILE_DIRECTORY = 'files';
	const UPLOAD_IMAGE_DIRECTORY = 'images';

	/**
	 * @var string
	 */
	private $temporaryDir;

	/**
	 * @var string
	 */
	private $fileDir;

	/**
	 * @var string
	 */
	private $imageDir;

	/**
	 * @var \SS6\ShopBundle\Component\FileUpload\FileNamingConvention
	 */
	private $fileNamingConvention;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @param string $temporaryDir
	 * @param string $fileDir
	 * @param string $imageDir
	 * @param \SS6\ShopBundle\Component\FileUpload\FileNamingConvention $fileNamingConvention
	 * @param \Symfony\Component\Filesystem\Filesystem $filesystem
	 */
	public function __construct($temporaryDir, $fileDir, $imageDir, FileNamingConvention $fileNamingConvention,
			Filesystem $filesystem) {
		$this->temporaryDir = $temporaryDir;
		$this->fileDir = $fileDir;
		$this->imageDir = $imageDir;
		$this->fileNamingConvention = $fileNamingConvention;
		$this->filesystem = $filesystem;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 */
	public function upload(UploadedFile $file) {
		if ($file->getError()) {
			throw new \SS6\ShopBundle\Component\FileUpload\Exception\UploadFailedException($file->getErrorMessage(), $file->getError());
		}

		$temporaryFilename = $this->getTemporaryFilename($file->getClientOriginalName());
		$file->move($this->getTemporaryDirectory(), $temporaryFilename);

		return $temporaryFilename;
	}

	/**
	 * @param string $filename
	 * @return bool
	 */
	public function tryDeleteTemporaryFile($filename) {
		if (!empty($filename)) {
			$filepath = $this->getTemporaryFilepath($filename);
			try {
				$this->filesystem->remove($filepath);
			} catch (\Symfony\Component\Filesystem\Exception\IOException $ex) {
				return false;
			}
		}
		return true;
	}

	/**
	 * @param string $filename
	 * @return string
	 */
	private function getTemporaryFilename($filename) {
		return TransformString::safeFilename(uniqid() . '__' . $filename);
	}

	/**
	 * @param string $temporaryFilename
	 * @return string
	 */
	public function getTemporaryFilepath($temporaryFilename) {
		return $this->getTemporaryDirectory() . DIRECTORY_SEPARATOR . TransformString::safeFilename($temporaryFilename);
	}

	/**
	 * @return string
	 */
	public function getTemporaryDirectory() {
		return $this->temporaryDir . DIRECTORY_SEPARATOR . self::TEMPORARY_DIRECTORY;
	}

	/**
	 * @param string $isImage
	 * @param string $category
	 * @param string|null $targetDirectory
	 * @return string
	 */
	public function getUploadDirectory($isImage, $category, $targetDirectory) {
		return ($isImage ? $this->imageDir : $this->fileDir)
			. DIRECTORY_SEPARATOR . $category
			. ($targetDirectory !== null ? DIRECTORY_SEPARATOR . $targetDirectory : '');
	}

	/**
	 * @param string $filename
	 * @param bool $isImage
	 * @param string $category
	 * @param string|null $targetDirectory
	 * @return string
	 */
	private function getTargetFilepath($filename, $isImage, $category, $targetDirectory) {
		return $this->getUploadDirectory($isImage, $category, $targetDirectory) . DIRECTORY_SEPARATOR . $filename;
	}

	/**
	 * @param string $temporaryFilename
	 * @return string
	 */
	public function getOriginalFilenameByTemporary($temporaryFilename) {
		$matches = [];
		if ($temporaryFilename && preg_match('/^.+?__(.+)$/', $temporaryFilename, $matches)) {
			return $matches[1];
		}
		return $temporaryFilename;
	}

	/**
	 * @param \SS6\ShopBundle\Component\FileUpload\EntityFileUploadInterface $entity
	 */
	public function preFlushEntity(EntityFileUploadInterface $entity) {
		$filesForUpload = $entity->getTemporaryFilesForUpload();
		foreach ($filesForUpload as $key => $fileForUpload) {
			/* @var $fileForUpload FileForUpload */
			$originalFilename = $this->getOriginalFilenameByTemporary($fileForUpload->getTemporaryFilename());
			$entity->setFileAsUploaded($key, $originalFilename);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Component\FileUpload\EntityFileUploadInterface $entity
	 */
	public function postFlushEntity(EntityFileUploadInterface $entity) {
		$filesForUpload = $entity->getTemporaryFilesForUpload();
		foreach ($filesForUpload as $fileForUpload) {
			/* @var $fileForUpload FileForUpload */
			$sourceFilepath = $this->getTemporaryFilepath($fileForUpload->getTemporaryFilename());
			$originalFilename = $this->fileNamingConvention->getFilenameByNamingConvention(
				$fileForUpload->getNameConventionType(),
				$fileForUpload->getTemporaryFilename(),
				$entity->getId()
			);
			$targetFilename = $this->getTargetFilepath(
				$originalFilename,
				$fileForUpload->isImage(),
				$fileForUpload->getCategory(),
				$fileForUpload->getTargetDirectory()
			);

			try {
				$this->filesystem->rename($sourceFilepath, $targetFilename, true);
			} catch (\Symfony\Component\Filesystem\Exception\IOException $ex) {
				$message = 'Failed to rename file from temporary directory to entity';
				throw new \SS6\ShopBundle\Component\FileUpload\Exception\MoveToEntityFailedException($message, $ex);
			}
		}
	}

}