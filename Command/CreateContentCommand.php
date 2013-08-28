<?php

namespace Netgen\EzscIpAddressBundle\Command;

use eZ\Publish\Core\Base\Exceptions\ContentFieldValidationException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateContentCommand extends ContainerAwareCommand
{
    /**
     * This method override configures on input argument for the content id
     */
    protected function configure()
    {
        $this->setName( 'ezsc:create_content' );
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param \Symfony\Component\Console\Input\InputInterface  $input  An InputInterface instance
     * @param \Symfony\Component\Console\Output\OutputInterface $output An OutputInterface instance
     *
     * @return null|integer null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract method is not implemented
     * @see    setCode()
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->getContainer()->get( "ezpublish.api.repository" );
        $contentService = $repository->getContentService();

        $contentCreate = $contentService->newContentCreateStruct(
            $repository->getContentTypeService()->loadContentTypeByIdentifier( "article" ),
            "eng-GB"
        );

        $introText =
<<<EOT
<?xml version="1.0" encoding="utf-8"?>
<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/"
    xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/"
    xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/">
    <paragraph>Intro text</paragraph>
</section>
EOT;

        $contentCreate->setField( "title", "Test article" );
        $contentCreate->setField( "intro", $introText );
        $contentCreate->setField( "ip", "126.123.235.46" );

        $locationCreate = $repository->getLocationService()->newLocationCreateStruct( 2 );

        $user = $repository->getUserService()->loadUser( 14 );
        $repository->setCurrentUser( $user );

        try
        {
            $draft = $contentService->createContent(
                $contentCreate,
                array( $locationCreate )
            );

            $contentService->publishVersion( $draft->versionInfo );
        }
        catch ( ContentFieldValidationException $e )
        {
            var_dump( $e->getFieldErrors() );
        }
    }
}
